<?php

namespace App\Workflow\Subscribers\CreateZip;

use App\Service\LoggerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\FileSystem\CloudFileSystemService;
use Symfony\Component\Workflow\Event\Event;
use App\Service\Notification\NotificationService;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Upload implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private KernelInterface $kernel;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationService;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;

	/**
	 * Upload constructor.
	 */
	public function __construct(
		LoggerService $logger,
		Registry $registry,
		KernelInterface $kernel,
		ParameterBagInterface $parameterBag,
		CloudFileSystemService $fileBucketService,
		NotificationService $notificationService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $logger;
		$this->registry = $registry;
		$this->kernel = $kernel;
		$this->parameterBag = $parameterBag;
		$this->notificationService = $notificationService;
		$this->fileBucketService = $fileBucketService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ZIP_CREATE);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.create_zip.completed.published' => 'uploadFiles',
		];
	}

	public function uploadFiles(Event $event)
	{
		try {
			$history = $event->getSubject();
			$wf = $this->registry->get($history, 'create_zip');
			$context = $history->getContext();
			$path = sprintf('lists/%s.json', $history->getName());
			if (isset($context['download_file'])) {
				$path = $context['download_file'];
			}
			$uploaded = false;
			$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_CP);
			if (isset($context['source_disk'])) {
				$this->fileBucketService->changeStorage($context['source_disk']);
			}
			if (isset($context['request']['link'])) {
				$filePath = sprintf('%s/var/%s', $this->kernel->getProjectDir(), $context['request']['link']);
				if (file_exists($filePath)) {
					$fileName = sprintf('%s/%s', $history->getName(), basename($filePath));
					if ($this->fileBucketService->exists($fileName)) {
						$this->fileBucketService->deleteFile($fileName);
					}
					$file = $this->fileBucketService->upload($fileName, $filePath);
					if (!$file) {
						throw new \Exception('unable to load the file to the source disk');
					}
					unlink($filePath);
					$context['request']['link'] = $this->fileBucketService->getTemporaryUrl($fileName);
					$history->setCloudName($fileName);
					$provider = $context['source_disk'];
					if (filter_var($context['source_disk'], FILTER_VALIDATE_URL)) {
						$provider = 'url_provider';
					}
					$history->setProvider($provider);
					$uploaded = true;
				}
			}
			if (isset($context['list_content']) && file_exists($context['list_content'])) {
				$this->fileBucketService->upload(sprintf('%s/lists/%s', $history->getName(), basename($context['list_content'])), $context['list_content']);
				unlink($context['list_content']);
				unset($context['list_content']);
			}

			if (!$uploaded) {
				$this->loggerSrv->alert('Zip file was not created due to no files were found');
			}
			if ($wf->can($history, 'finished')) {
				if ($this->fileBucketService->exists($path)) {
					$this->fileBucketService->deleteFile($path);
				}
				if ($context['statistics']['processedFiles'] === $context['statistics']['totalFiles']) {
					$context['info'] = sprintf('Link generated successfully: %d/%d', $context['statistics']['processedFiles'], $context['statistics']['totalFiles']);
					$context['status'] = 'success';
				} else {
					if ($context['statistics']['processedFiles']) {
						$context['info'] = sprintf('%d/%d: some files were not found', $context['statistics']['processedFiles'], $context['statistics']['totalFiles']);
						$context['status'] = 'warning';
					} else {
						$context['info'] = sprintf('%d/%d: not files found', $context['statistics']['processedFiles'], $context['statistics']['totalFiles']);
						$context['status'] = 'alert';
					}
				}
				$history->setContext($context);
				$data = [];
				switch ($context['notification_type']) {
					case NotificationService::NOTIFICATION_TYPE_TEAM:
						$data = [
							'message' => $context['info'],
							'status' => $context['status'],
							'date' => (new \DateTime())->format('Y-m-d'),
							'link' => (isset($context['request']['link'])) ? $context['request']['link'] : 'No File generated',
							'title' => $history->getName(),
						];
						break;
					case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
						$template = $this->parameterBag->get('app.postmark.tpl_id.workflow');
						if (!empty($template)) {
							$data = [
								'template' => $template,
								'workflow' => $history->getName(),
								'link' => (isset($context['request']['link'])) ? $context['request']['link'] : 'No File generated',
							];
						}
						break;
				}
				$this->notificationService->addNotification(
					$context['notification_type'],
					$context['notification_target'],
					$data,
					$history->getName()
				);
				$this->loggerSrv->addInfo("{$history->getName()}: Sending notification");
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'finished');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
