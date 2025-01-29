<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Connector\Xtm\Request\FileStatusRequest;
use App\Connector\Xtm\Request\ProjectFileRequest;
use App\Connector\Xtm\XtmConnector;
use App\Message\XtmProjectExtendedMessage;
use App\Model\Entity\AnalyticsProject;
use App\Model\Entity\XtmEditDistance;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmProjectExtendedMessageHandler
{
	use LockableTrait;

	private EntityManagerInterface $em;
	private XtmConnector $xtmConnector;
	private FileSystemService $fileSystemSrv;
	private LoggerService $loggerSrv;
	private ?Worksheet $worksheet;
	private int $entriesCount;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		XtmConnector $xtmConnector,
		FileSystemService $fileSystemSrv,
		?Worksheet $worksheet,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->worksheet = $worksheet;
		$this->xtmConnector = $xtmConnector;
		$this->fileSystemSrv = $fileSystemSrv;
	}

	public function __invoke(XtmProjectExtendedMessage $message): void
	{
		$limit = $message->getLimit();
		$analyticsProjects = $this->em->getRepository(AnalyticsProject::class)->findForTableExtendedProcessing($limit);
		$this->loggerSrv->addInfo(sprintf('Extended Table to process: %s', count($analyticsProjects)));

		/** @var AnalyticsProject $analyticsProject */
		foreach ($analyticsProjects as $analyticsProject) {
			$this->loggerSrv->addInfo(sprintf('Processing Analytics Project <entname> %s %s <entname>', $analyticsProject->getExternalId(), $analyticsProject->getTargetLanguageCode()));
			$this->loggerSrv->addInfo("Extended Table: for Analytics Project: {$analyticsProject->getExternalId()} {$analyticsProject->getTargetLanguageCode()}");
			$extendedTableFileId = $analyticsProject->getExtendedTableFileId() ?? null;
			if (!$extendedTableFileId) {
				$fileResponse = $this->xtmConnector->generateProjectFile($analyticsProject->getExternalId(), $analyticsProject->getTargetLanguageCode(), ProjectFileRequest::FILE_TYPE_EXCEL_EXTENDED_TABLE);

				if (!$fileResponse->isSuccessfull()) {
					$this->loggerSrv->addError('Warning! Error returned from XTM file generate');
					continue;
				}

				$data = $fileResponse->getData();
				$extendedTableFileId = intval($data['fileId']) ?? null;
				$analyticsProject->setExtendedTableFileId($extendedTableFileId);
				$this->em->persist($analyticsProject);
				$this->em->flush();
			}

			if (null === $analyticsProject->getExtendedTableFileId()) {
				$this->loggerSrv->addError('Warning! No File ID! Something went wrong!');
				continue;
			}

			$isGeneratedResponse = $this->xtmConnector->checkProjectFile($analyticsProject->getExternalId(), [$analyticsProject->getExtendedTableFileId()], ProjectFileRequest::FILE_TYPE_EXCEL_EXTENDED_TABLE);

			if (!$isGeneratedResponse->isSuccessfull()) {
				$this->loggerSrv->addError('<warning>Warning!</warning>] Error returned from XTM file check status. Cleaning FILEID for later check.');
				$this->loggerSrv->addInfo('Extended Table: Error returned from XTM file check status=>'.$isGeneratedResponse->getDetailedMessage());
				$analyticsProject->setExtendedTableFileId(null);
				$this->em->persist($analyticsProject);
				$this->em->flush();
				continue;
			}

			$fileStatusData = $isGeneratedResponse->getData();
			$fileStatus = $fileStatusData['status'] ?? FileStatusRequest::FILE_STATUS_ERROR;

			if (FileStatusRequest::FILE_STATUS_ERROR === strval($fileStatus)) {
				$this->loggerSrv->addError('<warning>Warning!</warning>] An error occured while generating Extended file');
				$analyticsProject->setExtendedTableFileId(null);
				$this->em->persist($analyticsProject);
				continue;
			} elseif (FileStatusRequest::FILE_STATUS_IN_PROGRESS === strval($fileStatus)) {
				$result = Helper::resultToString(Helper::PENDING);
				$this->loggerSrv->addWarning('<entval>'.$result.'</entval> In progress!');
				continue;
			} elseif (FileStatusRequest::FILE_STATUS_FINISHED === strval($fileStatus)) {
				$this->loggerSrv->addInfo('Extended Table: for Analytics Project: '.$fileStatus);

				$fileDownloadResponse = $this->xtmConnector->downloadProjectFile($analyticsProject->getExternalId(), $extendedTableFileId, ProjectFileRequest::FILE_TYPE_EXCEL_EXTENDED_TABLE);

				if (!$fileDownloadResponse->isSuccessfull()) {
					if (Response::HTTP_NOT_FOUND === $fileDownloadResponse->getHttpCode()) {
						$this->loggerSrv->addCritical('Generated file not found! ');

						$analyticsProject->setExtendedTableFileId(null);
						$this->em->persist($analyticsProject);

						$result = Helper::resultToString(Helper::NOT_FOUND);
						$this->loggerSrv->addWarning('<entval>'.$result.'</entval>'.
							' <error>Generated file not found!</error>');
						continue;
					}
					$this->loggerSrv->addWarning("Unable to fetch file data from XTM API. Project=> {$analyticsProject->getId()} FileId=>$extendedTableFileId
					HttpCode:{$fileDownloadResponse->getHttpCode()} Message:{$fileDownloadResponse->getDetailedMessage()}");
					$this->loggerSrv->addWarning('Unable to fetch file data from XTM API.');
					continue;
				}

				$content = $fileDownloadResponse->getRaw();
				$foldername = uniqid('xtm_folder');
				$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $foldername);
				$filePath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$foldername;

				$zipFilename = uniqid('xtm_file_');
				if (!$this->fileSystemSrv->createOrOverrideFile("$filePath/$zipFilename", $content)) {
					$msg = 'Unable to create local file from XTM response';
					$this->loggerSrv->addError("Extended Table: for Analytics Project: {$analyticsProject->getId()} and file $extendedTableFileId: $msg");
					$analyticsProject->setEditDistanceStatus($analyticsProject::ED_SKIPPED);
					$this->em->persist($analyticsProject);
					$this->em->flush();
					$this->loggerSrv->addWarning($msg);
					continue;
				}

				if (file_exists($filePath.DIRECTORY_SEPARATOR.$zipFilename)) {
					try {
						$zipExtractFolder = uniqid('xtm_zip_folder');
						$zipExtractPath = $filePath;
						$this->fileSystemSrv->createDirectory($zipExtractPath, $zipExtractFolder);
						$zipExtractFolder = $zipExtractPath.DIRECTORY_SEPARATOR.$zipExtractFolder;
						$filePath = $filePath.DIRECTORY_SEPARATOR.$zipFilename;
						$this->fileSystemSrv->unzipFile($filePath, $zipExtractFolder);
						$files = $this->fileSystemSrv->getContentDir($zipExtractFolder);

						if (count($files) > 1) {
							$msg = 'Analytic project is too old or data is corrupted. Excel file is not valid.';
							$this->loggerSrv->addError("Extended Table: for Analytics Project: {$analyticsProject->getId()} and file $extendedTableFileId: $msg");
							$this->loggerSrv->addWarning($msg);
							$analyticsProject->setEditDistanceStatus($analyticsProject::ED_SKIPPED);
							$this->em->persist($analyticsProject);
							$this->em->flush();
							continue;
						}
						$xlsFile = array_shift($files);
						$filePath = "$zipExtractFolder/$xlsFile";
						$reader = new Xlsx();
						$reader->setReadDataOnly(true);
						$spreadsheet = $reader->load($filePath);

						$this->worksheet = $spreadsheet->getActiveSheet();
						$this->entriesCount = 0;
						$targetIndexs = $this->getTargetColumnIndex();

						if (!$targetIndexs) {
							$msg = 'Unable to find column un text "translate" into excel file.';
							$this->loggerSrv->addError("Extended Table: for Analytics Project: {$analyticsProject->getId()} and file $extendedTableFileId: $msg");
							$this->loggerSrv->addWarning($msg);
							$analyticsProject->setEditDistanceStatus($analyticsProject::ED_SKIPPED);
							$this->em->persist($analyticsProject);
							$this->em->flush();
							continue;
						}

						$targetColumnIndex = $targetIndexs['columnIndex'];
						$targetRowIndex = $targetIndexs['rowIndex'];
						$rowsCount = $rowsZeroCount = $lowerScore = $higherScore = $averageScore = 0;
						foreach ($this->worksheet->getRowIterator() as $row) {
							$rowIndex = $row->getRowIndex();
							if ($rowIndex < $targetRowIndex) {
								continue;
							}
							$cellData = $this->worksheet->getCellByColumnAndRow($targetColumnIndex, $rowIndex);
							$previousColumn = $this->worksheet->getCellByColumnAndRow($targetColumnIndex - 1, $rowIndex)->getValue();
							$isValidValue = false;
							if (is_numeric($previousColumn)) {
								++$rowsCount;
								$isValidValue = true;
							}
							$value = $cellData->getValue();
							if ($isValidValue) {
								if (empty($value)) {
									++$rowsZeroCount;
								} else {
									$averageScore += $value;
									if (0 === $lowerScore) {
										$lowerScore = $value;
									}
									if ($value >= $higherScore) {
										$higherScore = $value;
									}
									if ($value <= $lowerScore) {
										$lowerScore = $value;
									}
								}
							}
						}

						$editDistanceObj = $analyticsProject->getXtmEditDistance();

						if (!$editDistanceObj) {
							$editDistanceObj = new XtmEditDistance();
							$editDistanceObj->setAnalyticsProject($analyticsProject);
						}
						$averageScore = 0 === $rowsCount ? 0 : $averageScore / $rowsCount;
						$editDistanceObj
							->setRowsCount($rowsCount)
							->setRowsZeroCount($rowsZeroCount)
							->setLowerScore($lowerScore)
							->setHigherScore($higherScore)
							->setAverageScore($averageScore);
						$analyticsProject->setProcessingStatus(AnalyticsProject::EXTENDED_TABLE_PROCESSED);

						$result = Helper::resultToString(Helper::UPDATED);
						$this->loggerSrv->addInfo("<entval>$result</entval> Count rows: $rowsCount Zero rows: $rowsZeroCount");

						$analyticsProject->setEditDistanceStatus($analyticsProject::ED_FINISHED);
						$this->em->persist($editDistanceObj);
					} catch (\Throwable $thr) {
						$this->loggerSrv->addCritical("Error processing Extended Table for Analytic Project {$analyticsProject->getId()}", $thr);
						$result = Helper::resultToString(Helper::NOT_CHANGED);
						$this->loggerSrv->addError('<entval>'.$result.'</entval> <error>Error: '.$thr->getMessage().'</error>');
					}
				}
			}

			$this->em->persist($analyticsProject);
			$this->em->flush();
			time_nanosleep(0, 200000000);
		}
	}

	public function getTargetColumnIndex(): ?array
	{
		foreach ($this->worksheet->getRowIterator() as $row) {
			$rowIndex = $row->getRowIndex();
			$columnKey = 0;
			foreach ($this->worksheet->getColumnIterator() as $column) {
				$data = $this->worksheet->getCellByColumnAndRow($columnKey, $rowIndex);
				if (str_contains($data->getValue(), 'translate')) {
					return [
						'rowIndex' => ++$rowIndex,
						'columnIndex' => $columnKey,
					];
				}
				++$columnKey;
			}
		}

		return null;
	}
}
