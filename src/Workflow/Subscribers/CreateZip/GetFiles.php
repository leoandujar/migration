<?php

namespace App\Workflow\Subscribers\CreateZip;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\FileSystem\CloudFileSystemService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GetFiles implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private KernelInterface $kernel;
	private CloudFileSystemService $fileBucketService;
	private EntityManagerInterface $em;

	/**
	 * GetListJson constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		KernelInterface $kernel,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->kernel = $kernel;
		$this->fileBucketService = $fileBucketService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ZIP_CREATE);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.create_zip.completed.prepared' => 'getFiles',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function getFiles(Event $event)
	{
		/**
		 * @var WFHistory $history
		 */
		$history = $event->getSubject();
		$wf = $this->registry->get($history, 'create_zip');
		$context = $history->getContext();
		try {
			$this->fileBucketService->changeStorage($context['working_disk']);
			$path = sprintf('%s/var/%s', $this->kernel->getProjectDir(), $history->getName());
			if (!file_exists($path)) {
				mkdir($path);
			}
			$fileSystem = new Filesystem();
			foreach ($context['files'] as &$file) {
				$resource = $this->fileBucketService->download($file['path']);
				if (!$resource) {
					if (!isset($context['error_files'])) {
						$context['error_files'] = [];
					}
					$file['missing'] = true;
					$context['error_files'][] = $file['path'];
					continue;
				}
				$fileName = $path.'/'.$file['name'];
				$fileSystem->appendToFile($fileName, $resource);
				++$context['statistics']['processedFiles'];
			}
			$this->loggerSrv->addInfo("{$history->getName()}: {$context['statistics']['processedFiles']} files downloaded");
			if ($wf->can($history, 'downloaded')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'downloaded');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
