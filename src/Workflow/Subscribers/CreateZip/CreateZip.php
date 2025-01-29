<?php

namespace App\Workflow\Subscribers\CreateZip;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Service\FileSystem\FileSystemService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateZip implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private KernelInterface $kernel;
	private EntityManagerInterface $em;

	/**
	 * CreateZip constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		KernelInterface $kernel,
		EntityManagerInterface $em
	) {
		$this->registry  = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->kernel    = $kernel;
		$this->em        = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ZIP_CREATE);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.create_zip.completed.downloaded' => 'createZip',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function createZip(Event $event)
	{
		/**
		 * @var WFHistory $history
		 */
		$history = $event->getSubject();
		$wf      = $this->registry->get($history, 'create_zip');
		$context = $history->getContext();
		if (count($context['files']) && $context['statistics']['processedFiles']) {
			$filesPath = sprintf('%s/var/%s', $this->kernel->getProjectDir(), $history->getName());
			$path      = sprintf('%s/var', $this->kernel->getProjectDir());
			$zipper    = new \ZipArchive();
			$now       = new \DateTime();
			$name      = sprintf('%s-%s.zip', $history->getName(), $now->format('Y-m-d-Hms'));
			if (isset($context['name'])) {
				$name = sprintf('%s-%s.zip', $context['name'], $now->format('Y-m-d-Hms'));
			}
			$zipName = utf8_encode(sprintf('%s/%s', $path, $name));
			$zipper->open($zipName, \ZipArchive::CREATE);
			foreach ($context['files'] as $file) {
				if (!isset($file['missing'])) {
					$downloadFileName = $file['name'];
					if (array_key_exists('prefix', $file)) {
						$downloadFileName = sprintf('%s%s', $file['prefix'], $file['name']);
					}
					$filePath = implode('/', [$filesPath, $file['name']]);
					if (!file_exists($filePath)) {
						$this->loggerSrv->addError(sprintf('file %s was not found', $file['name']));
						continue;
					}
					$zipper->addFile($filePath, $downloadFileName);
					unset($filePath);
				}
			}
			$zipper->close();
			FileSystemService::deleteDir(sprintf('%s/var/%s', $this->kernel->getProjectDir(), $history->getName()));
			$context['request']['link'] = $name;
			$context['info']            = 'File zip created successfully.';
			$context['status']          = 'success';
			$this->loggerSrv->addInfo("{$history->getName()}: {$name} zip created successfully.");
		}
		try {
			if ($wf->can($history, 'published')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'published');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
