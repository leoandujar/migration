<?php

namespace App\Workflow\Subscribers\Attestation;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DownloadFiles implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private EntityManagerInterface $em;
	private FileSystemService $fileSystemSrv;
	private CloudFileSystemService $fileBucketService;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketService,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->fileBucketService = $fileBucketService;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ATTESTATION);
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.attestation.completed.collect' => 'downloadFiles',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function downloadFiles(Event $event)
	{
		try {
			$this->loggerSrv->addInfo('Starting download files for Attestation filtered projects.');
			/** @var WFHistory $history */
			$history = $event->getSubject();
			$context = $history->getContext();
			$params = $context['params'];
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
			$filesInfo = $context['filesInfo'];
			$ocrType = $params['ocrType'];
			$fileList = [];
			$filesError = [];
			$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_PROJECTS);
			foreach ($filesInfo as $info) {
				$path = $info['path'];
				$folderContent = $this->fileBucketService->listContents($path);

				if ($folderContent) {
					if ('azure' === $ocrType) {
						foreach ($folderContent as $folderItem) {
							try {
								$itemPath = mb_convert_encoding($folderItem['path'], 'UTF-8', 'UTF-8');
								$fileName = basename($itemPath);
								$temporaryUrl = $this->fileBucketService->getTemporaryUrl($itemPath);
								if (empty($temporaryUrl)) {
									$filesError[] = $itemPath;
									$msg = "Could not get temporary url for file $itemPath. Skip it for now.";
									$this->monitorLogSrv->appendError([
										'reason' => 'temporary_url_error',
										'file' => $fileName,
									]);
									$this->loggerSrv->addError($msg);
									continue;
								}
								$fileList[] = [
									'name' => $fileName,
									'temporaryUrl' => $temporaryUrl,
									'language' => $info['language'],
									'project' => $info['project'],
								];
								$this->loggerSrv->addInfo("Generated temporal url for $itemPath");
							} catch (\Throwable $thr) {
								$this->loggerSrv->addError('Error generating temporal url for Attestation workflow.', $thr);
								continue;
							}
						}
					} else {
						foreach ($folderContent as $folderItem) {
							try {
								$itemPath = $folderItem['path'];
								$fileName = basename($itemPath);
								$fileContent = $this->fileBucketService->download($itemPath);
								if (!$fileContent) {
									$filesError[] = $itemPath;
									$msg = "Could not download file $itemPath. Skip it for now.";
									$this->monitorLogSrv->appendError([
										'reason' => 'download_file_error',
										'file' => $fileName,
									]);
									$this->loggerSrv->addError($msg);
									continue;
								}
								$fileList[] = [
									'file' => [$fileName => $fileContent],
									'language' => $info['language'],
									'project' => $info['project'],
								];
								$this->loggerSrv->addInfo("Downloaded file $itemPath");
							} catch (\Throwable $thr) {
								$this->loggerSrv->addError('Error generating temporal url for Attestation workflow.', $thr);
								continue;
							}
						}
					}
				}
			}

			if (!$fileList) {
				$msg = 'Unable to download the files content for Attestation workflow. No file content available';
				$this->loggerSrv->addWarning($msg);
				throw new BadRequestHttpException($msg);
			}

			if ('internal' === $ocrType) {
				$folderName = uniqid('attestation_input_');
				$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $folderName);
				$filePath = $context['inputFolderName'] = "{$this->fileSystemSrv->filesPath}/$folderName";
				$filesPathList = [];
				foreach ($fileList as $fileContent) {
					$language = $fileContent['language'];
					$project = $fileContent['project'];
					$key = key($fileContent['file']);
					$content = $fileContent['file'][$key];
					$filename = "$key";
					if ($this->fileSystemSrv->createOrOverrideFile("$filePath/$filename", $content)) {
						$filesPathList[] = [
							'file' => [$filename => "$filePath/$filename"],
							'language' => $language,
							'project' => $project,
						];
					}
				}

				if (!$filesPathList) {
					$msg = 'Unable to create the files from content for Attestation workflow. No file content available. Unable to continue.';
					$this->loggerSrv->addError($msg);
					throw new BadRequestHttpException($msg);
				}
			}

			$context['fileList'] = $fileList;
			$context['filesError'] = $filesError;
			$wf = $this->registry->get($history, 'attestation');
			if ($wf->can($history, 'download')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'download');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in downloadFiles step for Attestation workflow.', $thr);
			throw $thr;
		}
	}
}
