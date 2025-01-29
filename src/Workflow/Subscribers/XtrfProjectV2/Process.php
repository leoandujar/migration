<?php

namespace App\Workflow\Subscribers\XtrfProjectV2;

use App\Model\Entity\AVWorkflowMonitor;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Service\FileSystem\CloudFileSystemService;
use App\Connector\Xtrf\XtrfConnector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Connector\AzureCognitive\AzureVisionConnector;
use App\Workflow\HelperServices\MonitorLogService;
use App\Model\Repository\WorkflowMonitorRepository;

class Process implements EventSubscriberInterface
{
	private const OCR_TYPE_INTERNAL = 'internal';
	private const OCR_TYPE_AZURE = 'azure';
	private LoggerService $loggerSrv;
	private Registry $registry;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;
	private XtrfConnector $xtrfConnector;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private AzureVisionConnector $azureVisionConn;

	/**
	 * Upload constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		XtrfConnector $xtrfConnector,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em,
		AzureVisionConnector $azureVisionConn,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->fileBucketService = $fileBucketService;
		$this->xtrfConnector = $xtrfConnector;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->azureVisionConn = $azureVisionConn;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project_v2.completed.retrieve' => 'process',
		];
	}

	public function process(Event $event)
	{
		$this->loggerSrv->addInfo('Process files for XtrfProject Workflow');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		$wf = $this->registry->get($history, 'xtrf_project_v2');
		try {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
			$ocr = $context['ocr'];
			$files = $context['files'];
			$this->fileBucketService->changeStorage($context['sourceDisk']);

			if ($ocr) {
				$ocrType = $ocr['type'];
				$sourcePath = $context['sourcePath'];
				$folderName = "$sourcePath/ocr";
				if (self::OCR_TYPE_AZURE === $ocrType) {
					foreach ($files as $index => $file) {
						$fileName = $file['name'];
						$extension = pathinfo($fileName, PATHINFO_EXTENSION);
						$ocrFileName = basename($fileName, '.txt');
						$temporaryUrl = $file['temporaryUrl'];
						if (in_array($extension, ['pdf', 'jpg', 'png'])) {
							$this->loggerSrv->addInfo("Processing file in Azure Cognitive for file: $fileName");
							$analyzeResponse = $this->azureVisionConn->analyzeDocument($temporaryUrl, null, $ocr['config']['modelId']);
							if (empty($analyzeResponse) || !$analyzeResponse->isSuccessfull()) {
								$this->monitorLogSrv->appendError([
									'reason' => 'external_ocr_error',
									'file' => $fileName,
								]);
								$this->loggerSrv->addError("Unable to get file content from Azure Cognitive for attestation workflow: $fileName");
								continue;
							}
							$responseData = $analyzeResponse->getData();
							$content = $responseData['analyzeResult']['content'];
							$ocrPath = "$folderName/$ocrFileName";

							if ('token' === $context['projectFilesType']) {
								$response = $this->xtrfConnector->uploadProjectFile([[
									'name' => 'file',
									'contents' => $content,
									'filename' => $ocrFileName,
								]]);
								if (!$response || !$response->isSuccessfull()) {
									$this->loggerSrv->addError('Error uploading files for WF Project', [
										'message' => $response->getErrorMessage(),
									]);
									$this->monitorLogSrv->appendError([
										'reason' => 'external_ocr_error',
										'file' => $fileName,
									]);
									continue;
								}
								$context['files'][$index]['ocr'][] = [
									'name' => $ocrFileName,
									'token' => $response->getToken(),
								];
							} else {
								if (!$this->fileBucketService->write($ocrPath, $content)) {
									$this->loggerSrv->addError("File $ocrFileName was not written in $ocrPath");
									$this->monitorLogSrv->appendError([
										'reason' => 'external_ocr_error',
										'file' => $fileName,
									]);
								} else {
									$this->loggerSrv->addInfo("File $fileName was written in $ocrPath");
									$temporaryUrl = $this->fileBucketService->getTemporaryUrl($ocrPath);
									if (empty($temporaryUrl)) {
										$filesError[] = $ocrPath;
										$msg = "Could not get temporary url for file $ocrPath. Skip it for now.";
										$this->monitorLogSrv->appendError([
											'reason' => 'temporary_url_error',
											'file' => $fileName,
										]);
										$this->loggerSrv->addError($msg);
										continue;
									}

									$files[$index]['ocr'] = [
										'name' => $ocrFileName,
										'temporaryUrl' => $temporaryUrl,
									];
								}
							}
						}
					}
				}
			}

			$context['files'] = $files;
			if ($wf->can($history, 'process')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'process');
			}
		} catch (\Throwable $thr) {
			$this->monitorLogSrv->appendError([
				'message' => 'Workflow finished with error on Processing files',
			]);
			$this->loggerSrv->addError('Workflow finished with error on Processing files', $thr);
			$this->loggerSrv->alert($event, $thr);
			if ($wf->can($history, 'finish')) {
				$wf->apply($history, 'finish');
			}

			return;
		}
	}
}
