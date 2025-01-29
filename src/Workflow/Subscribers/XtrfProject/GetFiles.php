<?php

namespace App\Workflow\Subscribers\XtrfProject;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\FileSystem\CloudFileSystemService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GetFiles implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;

	/**
	 * GetListJson constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->fileBucketService = $fileBucketService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project.completed.initialized' => 'getFiles',
		];
	}

	/**
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function getFiles(Event $event)
	{
		/**
		 * @var WFHistory $history
		 */
		$history = $event->getSubject();
		$wf = $this->registry->get($history, 'xtrf_project');
		$this->loggerSrv->addInfo('Downloading files');
		$context = $history->getContext();
		try {
			$files = $context['ready_files'] ?? [];

			if (!$files && isset($context['source_disk'])) {
				$this->fileBucketService->changeStorage($context['source_disk']);
				if (!isset($context['download_prefix'])) {
					$this->loggerSrv->addWarning('Download prefix not defined.');
					if ($wf->can($history, 'finished')) {
						if ($history instanceof WFHistory) {
							$context['info'] = 'Download prefix not defined.';
							$context['status'] = 'warning';
							$context['request']['links'] = [];
							$history->setContext($context);
							if (!$this->em->isOpen()) {
								$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
							}
							$this->em->persist($history);
							$this->em->flush();
						}
						$wf->apply($history, 'finished');

						return;
					}
				}
				try {
					if (empty($context['ready_files'])) {
						$files = $this->fileBucketService->listContents($context['download_prefix'])->toArray();
						if (0 === count($files)) {
							$msg = sprintf('There is no file to download in path %s', $context['download_prefix']);
							$this->loggerSrv->addWarning($msg);
							if ($wf->can($history, 'finished')) {
								$context['info'] = $msg;
								$context['status'] = 'warning';
								$context['request']['links'] = [];
								$history->setContext($context);
								if (!$this->em->isOpen()) {
									$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
								}
								$this->em->persist($history);
								$this->em->flush();
								$wf->apply($history, 'finished');

								return;
							}
						}
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError($thr);
				}
			}

			if ($wf->can($history, 'downloaded')) {
				if ($history instanceof WFHistory) {
					$context['files'] = $files;
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'downloaded');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Workflow finished with error', $thr);
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
