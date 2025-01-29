<?php

namespace App\Workflow\Subscribers\XtmTm;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Service\FileSystem\CloudFileSystemService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UploadZips implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;

	/**
	 * DownloadFiles constructor.
	 */
	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em
	) {
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->fileBucketService = $fileBucketService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_TM);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_tm.completed.created' => 'upload',
		];
	}

	public function upload(Event $event)
	{
		try {
			/**
			 * @var $history WFHistory
			 */
			$history = $event->getSubject();
			$context = $history->getContext();
			$wf = $this->registry->get($history, 'xtm_tm');
			$checkStorage = $this->fileBucketService->checkStorage($context['source_disk']);
			if (!$checkStorage) {
				$msg = sprintf('source disk: %s not found', $context['source_disk']);
				$this->loggerSrv->addError($msg);
				$context['notify']['message'] = $msg;
				$history->setContext($context);
				if ($wf->can($history, 'notify')) {
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
					$wf->apply($history, 'notify');
				}

				return;
			}
			$this->fileBucketService->changeStorage($context['source_disk']);
			if ('true' == $context['batch']) {
				foreach ($context['output_files'] as $key => $file) {
					$fileName = sprintf('%s/%s', $context['upload_path'], basename($file));
					$this->fileBucketService->upload($fileName, $file);
					unlink($file);
					unset($context['output_files'][$key]);
				}
			} else {
				foreach ($context['files'] as $key => $file) {
					$fileName = sprintf('%s/%s-%d.zip', $context['upload_path'], $context['output_name'] ?? basename($file['path']), (new \DateTime())->getTimestamp());
					$this->fileBucketService->upload($fileName, $file['path']);
					unlink($file['path']);
					unset($context['files'][$key]);
					$file['public_link'] = $this->fileBucketService->getTemporaryUrl($fileName, $context['az_expiration_link']);
					$context['files'][$key] = $fileName;
				}
			}
			$history->setContext($context);
			if ($wf->can($history, 'uploaded')) {
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'uploaded');
				$this->loggerSrv->addInfo('XTMTM WF FINISHED SUCCESSFULLY');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
