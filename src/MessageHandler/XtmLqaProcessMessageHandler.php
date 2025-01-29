<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Connector\Xtm\Request\FileStatusRequest;
use App\Connector\Xtm\XtmConnector;
use App\Linker\Managers\LqaIssueTypeMappingManager;
use App\Message\XtmLqaProcessMessage;
use App\Model\Entity\AnalyticsProject;
use App\Model\Entity\LqaIssue;
use App\Model\Entity\LqaIssueTypeMapping;
use App\Model\Entity\XtmMetrics;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmLqaProcessMessageHandler
{
	private mixed $worksheet;
	private int $wordCount = 0;
	private int $entriesCount = 0;
	private FileSystemService $fileSystemSrv;
	private array $multipliers = [
		'neutral' => 0,
		'minor' => 1,
		'major' => 5,
		'critical' => 9,
	];
	private LqaIssueTypeMappingManager $mappingManager;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private XtmConnector $xtmConnector;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		XtmConnector $xtmConnector,
		FileSystemService $fileSystemSrv,
		LqaIssueTypeMappingManager $mappingManager,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->mappingManager = $mappingManager;
		$this->xtmConnector = $xtmConnector;
		$this->fileSystemSrv = $fileSystemSrv;
	}

	public function __invoke(XtmLqaProcessMessage $message): void
	{
		$start = $message->getStart();
		$limit = $message->getLimit();
		$one = $message->getOne();

		$this->loggerSrv->addInfo('Fetching and processing LQA reports of finished analytics projects');

		if ($one) {
			$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->findProjectForLqaProcessing($one);
		} else {
			$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->findForLqaProcessing($limit);
		}

		$this->loggerSrv->addInfo('LQA to process: '.count($analyticsProjects));

		foreach ($analyticsProjects as $analyticsProject) {
			$this->loggerSrv->addInfo('Analytics Project '.$analyticsProject->getExternalId().' '.
				$analyticsProject->getTargetLanguageCode());
			$this->loggerSrv->addInfo('LQA: for Analytics Project: '.$analyticsProject->getExternalId().' '.
				$analyticsProject->getTargetLanguageCode());

			if (null === $analyticsProject->getLqaFileId()) {
				try {
					$fileResponse =
						$this->xtmConnector->generateProjectFile($analyticsProject->getExternalId(), $analyticsProject->getTargetLanguageCode());
				} catch (ClientException $e) {
					$this->loggerSrv->addWarning('Unable to fetch data from API. '.$e->getCode().': '.$e->getMessage());
					$this->loggerSrv->addInfo('[Warning!] Connection error. '.$e->getCode().': '.
						$e->getMessage());
					continue;
				}

				if (!$fileResponse->isSuccessfull()) {
					$this->loggerSrv->addInfo('[Warning!] Error returned from XTM file generate');
					continue;
				}

				$data = $fileResponse->getData();
				$lqaFileId = intval($data['fileId']) ?? null;
				$analyticsProject->setLqaFileId($lqaFileId);
				$this->em->flush();
			}

			if (null === $analyticsProject->getLqaFileId()) {
				$this->loggerSrv->addInfo('[Warning!] No File ID! Something went wrong!');
				continue;
			}

			try {
				$isGeneratedResponse = $this->xtmConnector->checkProjectFile($analyticsProject->getExternalId(), [$analyticsProject->getLqaFileId()]);
			} catch (ClientException $e) {
				$this->loggerSrv->addWarning('Unable to fetch data from API. '.$e->getCode().': '.$e->getMessage());
				$this->loggerSrv->addInfo('[Warning!] Connection error. '.$e->getCode().': '.
					$e->getMessage());
				continue;
			}

			if (!$isGeneratedResponse->isSuccessfull()) {
				$this->loggerSrv->addInfo('[Warning!] Error returned from XTM file check status');
				continue;
			}

			$fileStatusData = $isGeneratedResponse->getData();
			$fileStatus = $fileStatusData['status'] ?? FileStatusRequest::FILE_STATUS_ERROR;
			if (FileStatusRequest::FILE_STATUS_ERROR == strval($fileStatus)) {
				$this->loggerSrv->addInfo('[Warning!] An error occured while generating LQA file');
				$analyticsProject->setLqaFileId(null);
				$this->em->persist($analyticsProject);
				continue;
			} elseif (FileStatusRequest::FILE_STATUS_IN_PROGRESS == strval($fileStatus)) {
				$result = Helper::resultToString(Helper::PENDING);
				$this->loggerSrv->addInfo('=>'.$result.' In progress!');
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
						$this->loggerSrv->addInfo('=>'.$result.' Got Error: Generated file not found!');
						continue;
					}
					$this->loggerSrv->addWarning('Unable to fetch file data from XTM API. '.$fileDownloadResponse->getHttpCode().': '.$fileDownloadResponse->getDetailedMessage());
					$this->loggerSrv->addInfo('[Warning!] Connection error. '.$fileDownloadResponse->getHttpCode().': '.
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
					$this->loggerSrv->addInfo($msg);
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

						$this->wordCount = $this->em->getRepository(XtmMetrics::class)->getWordCountByAnalyticsProject($analyticsProject);

						$issueTypeMapping = null;
						$level = 0;

						foreach ($this->worksheet->getRowIterator() as $row) {
							$rowIndex = $row->getRowIndex();
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
							$issueTypeMapping = $this->em->getRepository(LqaIssueTypeMapping::class)->findOneByNameAndParent($rowLabel, $issueParents[$level]);

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

								$this->processRow($analyticsProject, $previousIssueTypeMapping, $rowIndex - 1);
							}
						}

						if ($level > 0) {
							$this->processRow($analyticsProject, $issueTypeMapping, $rowIndex);
						}
						unset($issueParents);
						$analyticsProject->setLqaProcessed(true);

					} catch (\Throwable $thr) {
						$this->loggerSrv->addCritical($thr->getMessage(), $thr->getTrace());

						$result = Helper::resultToString(Helper::NOT_CHANGED);
						$this->loggerSrv->addInfo($result.' Error: '.$thr->getMessage());
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
			$this->loggerSrv->addInfo($result.'Issues rows: '.$this->entriesCount);
			time_nanosleep(0, 200000000);

		}
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
			$this->em->getRepository(LqaIssue::class)->findOneByAnalyticsProjectAndTypeMapping($analyticsProject, $issueTypeMapping);
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

	protected function calculatePenalty(LqaIssue $lqaEntry): float|int
	{
		return ($this->multipliers['neutral'] * $lqaEntry->getNeutral()) + ($this->multipliers['minor'] * $lqaEntry->getMinor()) + ($this->multipliers['major'] * $lqaEntry->getMajor()) + ($this->multipliers['critical'] * $lqaEntry->getCritical());
	}
}
