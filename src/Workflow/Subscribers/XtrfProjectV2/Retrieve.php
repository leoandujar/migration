<?php

namespace App\Workflow\Subscribers\XtrfProjectV2;

use App\Connector\Xtrf\XtrfConnector;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Model\Entity\AVWorkflowMonitor;
use App\Workflow\HelperServices\MonitorLogService;
use App\Service\FileSystem\CloudFileSystemService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Retrieve implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private XtrfConnector $xtrfConnector;
	private WorkflowMonitorRepository $wfMonitorRepo;

	/**
	 * GetListJson constructor.
	 *
	 * @param MonitorLogService         $monitorLogSrv ,
	 * @param WorkflowMonitorRepository $wfMonitorRepo ,
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		XtrfConnector $xtrfConnector
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->fileBucketService = $fileBucketService;
		$this->em = $em;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->xtrfConnector = $xtrfConnector;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project_v2.completed.initialize' => 'retrieve',
		];
	}

	/**
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function retrieve(Event $event)
	{
		$this->loggerSrv->addInfo('Retrieving files for XtrfProject Workflow');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();

		/** @var AVWorkflowMonitor $monitorObj */
		$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
		if ($monitorObj) {
			$this->monitorLogSrv->setMonitor($monitorObj);
		}

		$wf = $this->registry->get($history, 'xtrf_project_v2');

		try {
			if (!$this->fileBucketService->checkStorage($context['sourceDisk'])) {
				$this->loggerSrv->addWarning('Storage not exists');
				if ($wf->can($history, 'finish')) {
					$this->monitorLogSrv->appendError([
						'reason' => 'storage_not_exists',
						'message' => 'Storage not exists',
					]);
					if ($history instanceof WFHistory) {
						$context['info'] = 'Storage not exists';
						$context['status'] = 'warning';
						$context['request']['links'] = [];
						$history->setContext($context);
						if (!$this->em->isOpen()) {
							$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
						}
						$this->em->persist($history);
						$this->em->flush();
					}
					$wf->apply($history, 'finish');

					return;
				}
			}
			$this->fileBucketService->changeStorage($context['sourceDisk']);

			try {
				$filesList = $this->fileBucketService->listContents($context['sourcePath'])->toArray();
				if (0 === count($filesList)) {
					$msg = sprintf('There is no files to download in path %s', $context['sourcePath']);
					$this->loggerSrv->addWarning($msg);
					$this->monitorLogSrv->appendError([
						'reason' => 'no_files',
						'message' => $msg,
					]);
					if ($wf->can($history, 'finish')) {
						$context['info'] = $msg;
						$context['status'] = 'warning';
						$context['request']['links'] = [];
						$history->setContext($context);
						if (!$this->em->isOpen()) {
							$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
						}
						$this->em->persist($history);
						$this->em->flush();
						$wf->apply($history, 'finish');

						return;
					}
				}
				$files = [];
				if ('token' === $context['projectFilesType']) {
					foreach ($filesList as $index => $file) {
						$itemPath = mb_convert_encoding($file['path'], 'UTF-8', 'UTF-8');
						$fileName = basename($itemPath);
						$file = $this->fileBucketService->download($itemPath);
						$this->loggerSrv->addInfo("Downloaded file $fileName for Workflow XtrfProject");

						$response = null;

						$response = $this->xtrfConnector->uploadProjectFile([[
							'name' => 'file',
							'contents' => $file,
							'filename' => $fileName,
						]]);
						if (!$response || !$response->isSuccessfull()) {
							$this->loggerSrv->addError('Error uploading files for WF Project', [
								'message' => $response->getErrorMessage(),
							]);
							$this->monitorLogSrv->appendError([
								'reason' => 'upload_file',
								'file' => $fileName,
							]);
							continue;
						}
						if ($this->fileBucketService->deleteFile($itemPath)) {
							$this->loggerSrv->addInfo("Deleted file $fileName for Workflow XtrfProject");
						} else {
							$this->loggerSrv->addWarning("Error deleting file $fileName for Workflow XtrfProject");
						}
						$files[$index] = [
							'name' => $fileName,
							'path' => $itemPath,
							'token' => $response->getToken(),
						];
					}
				} else {
					foreach ($filesList as $index => $file) {
						$itemPath = mb_convert_encoding($file['path'], 'UTF-8', 'UTF-8');
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
						$files[$index] = [
							'name' => $fileName,
							'path' => $itemPath,
							'temporaryUrl' => $temporaryUrl,
						];
					}
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError($thr);
			}

			if ($wf->can($history, 'retrieve')) {
				if ($history instanceof WFHistory) {
					$context['files'] = $files;
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'retrieve');
			}
		} catch (\Throwable $thr) {
			$this->monitorLogSrv->appendError([
				'message' => 'Workflow finished with error on Retrieving files',
			]);
			$this->loggerSrv->addError('Workflow finished with error on Retrieve files', $thr);
			$this->loggerSrv->alert($event, $thr);
			if ($wf->can($history, 'finish')) {
				$wf->apply($history, 'finish');
			}

			return;
		}
	}
}
