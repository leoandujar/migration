<?php

namespace App\Command\Services;

use App\Connector\Xtm\Request\FileStatusRequest;
use App\Connector\Xtm\XtmConnector;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Model\Entity\LqaIssue;
use App\Connector\XtmConnector as OldXtmConnector;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Entity\LqaIssueTypeMapping;
use GuzzleHttp\Exception\ClientException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Model\Repository\XtmMetricsRepository;
use App\Model\Repository\LqaIssueRepository;
use Symfony\Component\Console\Input\InputInterface;
use App\Linker\Managers\LqaIssueTypeMappingManager;
use App\Model\Repository\AnalyticsProjectRepository;
use Symfony\Component\Console\Output\OutputInterface;
use App\Model\Repository\LqaIssueTypeMappingRepository;
use Symfony\Component\HttpFoundation\Response;

class UpdateLqaService
{
	private mixed $worksheet;
	private int $wordCount = 0;
	private int $entriesCount = 0;
	private array $multipliers = [
		'neutral' => 0,
		'minor' => 1,
		'major' => 5,
		'critical' => 9,
	];
	private LoggerService $loggerSrv;
	private OldXtmConnector $connector;
	private EntityManagerInterface $em;
	private XtmConnector $xtmConnector;
	private FileSystemService $fileSystemSrv;
	private LqaIssueRepository $issueRepository;
	private AnalyticsProjectRepository $repository;
	private XtmMetricsRepository $metricsRepository;
	private LqaIssueTypeMappingManager $mappingManager;
	private LqaIssueTypeMappingRepository $issueTypeMappingRepository;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		OldXtmConnector $connector,
		XtmConnector $xtmConnector,
		FileSystemService $fileSystemSrv,
		LqaIssueRepository $issueRepository,
		AnalyticsProjectRepository $repository,
		XtmMetricsRepository $metricsRepository,
		LqaIssueTypeMappingManager $mappingManager,
		LqaIssueTypeMappingRepository $issueTypeMappingRepository
	) {
		$this->em = $em;
		$this->connector = $connector;
		$this->loggerSrv = $loggerSrv;
		$this->repository = $repository;
		$this->xtmConnector = $xtmConnector;
		$this->mappingManager = $mappingManager;
		$this->issueRepository = $issueRepository;
		$this->metricsRepository = $metricsRepository;
		$this->issueTypeMappingRepository = $issueTypeMappingRepository;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->fileSystemSrv = $fileSystemSrv;
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		if ($input->getOption('one')) {
			$analyticsProjects = $this->repository->findProjectForLqaProcessing($input->getOption('one'));
		} else {
			$analyticsProjects = $this->repository->findForLqaProcessing($input->getOption('limit'));
		}

		$output->writeln('LQA to process: '.count($analyticsProjects));

		foreach ($analyticsProjects as $analyticsProject) {
			$output->write('Analytics Project <entname>'.$analyticsProject->getExternalId().' '.
				$analyticsProject->getTargetLanguageCode().'</entname>: ');
			$this->loggerSrv->addInfo('LQA: for Analytics Project: '.$analyticsProject->getExternalId().' '.
				$analyticsProject->getTargetLanguageCode());
			if (null === $analyticsProject->getLqaFileId()) {
				try {
					$fileResponse =
						$this->xtmConnector->generateProjectFile($analyticsProject->getExternalId(), $analyticsProject->getTargetLanguageCode());
				} catch (ClientException $e) {
					$this->loggerSrv->addWarning('Unable to fetch data from API. '.$e->getCode().': '.$e->getMessage());
					$output->writeln('<warning>Warning!</warning>] Connection error. '.$e->getCode().': '.
						$e->getMessage());
					continue;
				}

				if (!$fileResponse->isSuccessfull()) {
					$output->writeln('<warning>Warning!</warning>] Error returned from XTM file generate');
					continue;
				}

				$data = $fileResponse->getData();
				$lqaFileId = intval($data['fileId']) ?? null;
				$analyticsProject->setLqaFileId($lqaFileId);
				$this->em->flush();
			}

			if (null === $analyticsProject->getLqaFileId()) {
				$output->writeln('<warning>Warning!</warning>] No File ID! Something went wrong!');
				continue;
			}

			try {
				$isGeneratedResponse = $this->xtmConnector->checkProjectFile($analyticsProject->getExternalId(), [$analyticsProject->getLqaFileId()]);
			} catch (ClientException $e) {
				$this->loggerSrv->addWarning('Unable to fetch data from API. '.$e->getCode().': '.$e->getMessage());
				$output->writeln('<warning>Warning!</warning>] Connection error. '.$e->getCode().': '.
					$e->getMessage());
				continue;
			}

			if (!$isGeneratedResponse->isSuccessfull()) {
				$output->writeln('<warning>Warning!</warning>] Error returned from XTM file check status');
				continue;
			}

			$fileStatusData = $isGeneratedResponse->getData();
			$fileStatus = $fileStatusData['status'] ?? FileStatusRequest::FILE_STATUS_ERROR;
			if (FileStatusRequest::FILE_STATUS_ERROR == strval($fileStatus)) {
				$output->writeln('<warning>Warning!</warning>] An error occured while generating LQA file');
				$analyticsProject->setLqaFileId(null);
				$this->em->persist($analyticsProject);
				continue;
			} elseif (FileStatusRequest::FILE_STATUS_IN_PROGRESS == strval($fileStatus)) {
				$result = Helper::resultToString(Helper::PENDING);
				$output->writeln('<entval>'.$result.'</entval> In progress!');
				continue;
			}

			if (FileStatusRequest::FILE_STATUS_FINISHED == strval($fileStatus)) {
				$this->loggerSrv->addInfo('LQA: for Analytics Project: '.$fileStatus);

				$fileDownloadResponse = $this->xtmConnector->downloadProjectFile($analyticsProject->getExternalId(), $analyticsProject->getLqaFileId());

				if (!$fileDownloadResponse->isSuccessfull()) {
					if (Response::HTTP_NOT_FOUND === $fileDownloadResponse->getHttpCode()) {
						$this->loggerSrv->addCritical('Generated file not found! ');

						$analyticsProject->setLqaFileId(null);
						$this->em->persist($analyticsProject);

						$result = Helper::resultToString(Helper::NOT_FOUND);
						$output->writeln('<entval>'.$result.'</entval>'.
							' <error>Generated file not found!</error>');
						continue;
					}
					$this->loggerSrv->addWarning('Unable to fetch file data from XTM API. '.$fileDownloadResponse->getHttpCode().': '.$fileDownloadResponse->getDetailedMessage());
					$output->writeln('<warning>Warning!</warning>] Connection error. '.$fileDownloadResponse->getHttpCode().': '.
						$fileDownloadResponse->getDetailedMessage());
					continue;
				}

				$content = $fileDownloadResponse->getRaw();
				$foldername = uniqid('xtm_folder');
				$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $foldername);
				$filePath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$foldername;

				$zipFilename = uniqid('xtm_file_');
				if (!$this->fileSystemSrv->createOrOverrideFile("$filePath/$zipFilename", $content)) {
					$msg = 'Unable to create local file from XTM response';
					$this->loggerSrv->addError("LQA: for Analytics Project: {$analyticsProject->getId()} and file $lqaFileId: $msg");
					$output->writeln('<entval>'.$msg.'</entval>');
					continue;
				}

				if (file_exists($filePath.DIRECTORY_SEPARATOR.$zipFilename)) {
					try {
						$filePath = $filePath.DIRECTORY_SEPARATOR.$zipFilename;
						$reader = new Xlsx();
						$reader->setReadDataOnly(true);
						$spreadsheet = $reader->load($filePath);

						$this->worksheet = $spreadsheet->getActiveSheet();
						$tableFound = false;
						$dataFound = false;

						$issueParents = [0 => null];
						$this->entriesCount = 0;

						$this->readMultipliers();

						$this->wordCount = $this->metricsRepository->getWordCountByAnalyticsProject($analyticsProject);

						$issueTypeMapping = null;
						$level = 0;

						foreach ($this->worksheet->getRowIterator() as $row) {
							$rowIndex = $row->getRowIndex();
							// foreach ($value as $row) {
							// 3-ifs magic to skip the irrelevant rows
							if ('Language' == $this->worksheet->getCellByColumnAndRow(1, $rowIndex)) {
								$tableFound = true;
							}
							if ($tableFound && 'TOTAL' == $this->worksheet->getCellByColumnAndRow(1, $rowIndex)) {
								$dataFound = true;
								continue;
							}
							if (false === $dataFound) {
								continue;
							}

							$previousLevel = $level;
							$rowLabel = strval($this->worksheet->getCellByColumnAndRow(1, $rowIndex)->getValue());
							$level = substr_count($rowLabel, '   ');
							$rowLabel = $this->mappingManager->normalizeName($rowLabel);

							$previousIssueTypeMapping = $issueTypeMapping;
							$issueTypeMapping = $this->issueTypeMappingRepository->findOneByNameAndParent($rowLabel, $issueParents[$level]);

							if (null === $issueTypeMapping) {
								$issueTypeMapping = new LqaIssueTypeMapping();
								$issueTypeMapping->setName($rowLabel)
									->setActive(false)
									->setWeight(1)
									->setParent($issueParents[$level]);
								$this->em->persist($issueTypeMapping);
								$this->em->flush();
								$this->em->refresh($issueTypeMapping);

								$element = [
									'Parent' => $issueParents[$level],
									'IssueType' => $rowLabel,
									'active' => false,
								];

								$result = $this->mappingManager->updateEntry($element);
								$issueTypeMapping = $result['entity'];
								$alertMessage =
									'LQA Issue Type "'.$rowLabel.
									'" was not found. Entry added to LQA Issue Type Mapping table and require verification.';

								$this->loggerSrv->addWarning($alertMessage);
							}
							$issueParents[$level + 1] = $issueTypeMapping;

							if ($level < $previousLevel) {
								// Do all with previous line
								$this->processRow($analyticsProject, $previousIssueTypeMapping, $rowIndex - 1);
							}
						}
						if ($level > 0) {
							$this->processRow($analyticsProject, $issueTypeMapping, $rowIndex);
						}
						unset($issueParents);
						$analyticsProject->setLqaProcessed(true);
					} catch (\Exception $e) {
						$this->loggerSrv->addCritical($e->getMessage(), $e->getTrace());

						$result = Helper::resultToString(Helper::NOT_CHANGED);
						$output->writeln('<entval>'.$result.'</entval> <error>Error: '.$e->getMessage().
							'</error>');
					} finally {
						$dirPath = dirname($filePath);
						unlink($filePath);
						rmdir($dirPath);
					}
				}
			}

			$this->em->persist($analyticsProject);
			$this->em->flush();

			$result = Helper::resultToString(Helper::UPDATED);
			$output->writeln('<entval>'.$result.'</entval> Issues rows: '.$this->entriesCount);
			time_nanosleep(0, 200000000);
		}
	}

	protected function extractUrl(): string
	{
		$request = $this->connector->getLastRequest();
		$url = preg_replace('/^.+fileURL\>(.+)\<\/fileURL.+$/', '\\1', $request['resBody']);
		$url = explode('/', $url);
		$filename = array_pop($url);
		$filename = urlencode($filename);
		$filename = strtr($filename, [
			'+' => '%20',
			'%26amp%3B' => '%26',
		]);
		$url[] = $filename;

		return implode('/', $url);
	}

	protected function calculatePenalty(LqaIssue $lqaEntry): float|int
	{
		return ($this->multipliers['neutral'] * $lqaEntry->getNeutral()) + ($this->multipliers['minor'] * $lqaEntry->getMinor()) + ($this->multipliers['major'] * $lqaEntry->getMajor()) + ($this->multipliers['critical'] * $lqaEntry->getCritical());
	}

	protected function readMultipliers(): void
	{
		$this->multipliers['neutral'] = intval($this->worksheet->getCellByColumnAndRow(2, 2)->getValue());
		$this->multipliers['minor'] = intval($this->worksheet->getCellByColumnAndRow(2, 3)->getValue());
		$this->multipliers['major'] = intval($this->worksheet->getCellByColumnAndRow(2, 4)->getValue());
		$this->multipliers['critical'] = intval($this->worksheet->getCellByColumnAndRow(2, 5)->getValue());
	}

	private function processRow($analyticsProject, $issueTypeMapping, $rowIndex): void
	{
		$lqaEntry =
			$this->issueRepository->findOneByAnalyticsProjectAndTypeMapping($analyticsProject, $issueTypeMapping);
		if (null === $lqaEntry) {
			$lqaEntry = new LqaIssue();
			$lqaEntry->setAnalyticsProject($analyticsProject)
				->setLqaIssueTypeMapping($issueTypeMapping);
			$created = true;
		} else {
			$created = false;
		}

		$lqaEntry
			->setWeight(intval($this->worksheet->getCellByColumnAndRow(2, $rowIndex)->getValue()))
			->setNeutral(intval($this->worksheet->getCellByColumnAndRow(3, $rowIndex)->getValue()))
			->setMinor(intval($this->worksheet->getCellByColumnAndRow(4, $rowIndex)->getValue()))
			->setMajor(intval($this->worksheet->getCellByColumnAndRow(5, $rowIndex)->getValue()))
			->setCritical(intval($this->worksheet->getCellByColumnAndRow(6, $rowIndex)->getValue()));
		$lqaEntry->setPenaltyRaw($this->calculatePenalty($lqaEntry));
		$lqaEntry->setPenaltyAdjusted($lqaEntry->getPenaltyRaw() * $lqaEntry->getWeight());
		$lqaEntry->setTargetSubscore((1 - ($lqaEntry->getPenaltyAdjusted() / $this->wordCount)) * 100);

		$this->em->persist($lqaEntry);
		++$this->entriesCount;
	}
}
