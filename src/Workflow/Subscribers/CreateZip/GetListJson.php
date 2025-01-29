<?php

namespace App\Workflow\Subscribers\CreateZip;

use App\Model\Entity\Task;
use App\Service\LoggerService;
use App\Model\Entity\Project;
use App\Model\Entity\Activity;
use App\Model\Entity\WFHistory;
use App\Service\FileSystem\CloudFileSystemService;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\ProviderInvoice;
use App\Connector\Xtrf\XtrfConnector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use App\Connector\Utils\ConnectorsUtils;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GetListJson implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private KernelInterface $kernel;
	private CloudFileSystemService $fileBucketService;
	private ConnectorsUtils $connectorsUtils;
	private XtrfConnector $xtrfConnector;
	private EntityManagerInterface $em;

	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		KernelInterface $kernel,
		CloudFileSystemService $fileBucketService,
		ConnectorsUtils $connectorsUtils,
		XtrfConnector $xtrfConnector,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->kernel = $kernel;
		$this->fileBucketService = $fileBucketService;
		$this->connectorsUtils = $connectorsUtils;
		$this->xtrfConnector = $xtrfConnector;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ZIP_CREATE);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.create_zip.completed.initialized' => 'getFiles',
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
		$path = sprintf('lists/%s.json', $history->getName());
		if (isset($context['download_file'])) {
			$path = $context['download_file'];
		}
		$contentPath = sprintf('%s/var/%s-%s.json', $this->kernel->getProjectDir(), $history->getName(), (new \DateTime())->format('Y-m-dTH:i:s'));
		try {
			$list = [];
			$file = ['totalFiles' => 0];
			if (false === filter_var($context['source_disk'], FILTER_VALIDATE_URL)) {
				$this->fileBucketService->changeStorage($context['source_disk']);
				$content = $this->fileBucketService->download($path);
				$list = json_decode($content, true);
				$file['totalFiles'] = count($list);
				$reader = fopen($contentPath, 'a+');
				fwrite($reader, $content, strlen($content));
				fclose($reader);
				$context['list_content'] = $contentPath;
			} else {
				$content = $this->connectorsUtils->translateUrlToBrowser($context['source_disk']);
				$dataBrowser = $this->xtrfConnector->getDataBrowser($content);
				$ids = [];
				foreach ($dataBrowser->getRaw()['rows'] as $item) {
					$ids[] = $item['id'];
				}
				$context['data-browser-ids'] = $ids;
				$entityClass = null;
				if (isset($context['entity'])) {
					switch ($context['entity']) {
						case 'activity':
							$entityClass = Activity::class;
							break;
						case 'task':
							$entityClass = Task::class;
							break;
						case 'project':
							$entityClass = Project::class;
							break;
						case 'customer_invoice':
							$entityClass = CustomerInvoice::class;
							break;
						case 'provider_invoice':
							$entityClass = ProviderInvoice::class;
							break;
						default:
							$this->loggerSrv->addWarning("Provided entity {$context['entity']} scope not supported.");
							break;
					}
				}
				if (null !== $entityClass) {
					$repository = $this->em->getRepository($entityClass);
					$functions = [];
					if (isset($context['properties'])) {
						$properties = explode(',', $context['properties']);
						foreach ($properties as $property) {
							$method = str_replace(' ', '', ucwords(str_replace('_', ' ', $property)));
							$functions[] = sprintf('get%s', $method);
						}
					}
					$key = 0;
					foreach ($context['data-browser-ids'] as $item) {
						$entity = $repository->find($item);
						if (null !== $entity) {
							foreach ($functions as $function) {
								$newFile = $entity->{$function}();
								$pathInfo = pathinfo($newFile);
								++$file['totalFiles'];
								$matches = [];
								if (1 === preg_match('/^\/home\/[a-z]+\/[a-z]+\/[0-9]{2}_[a-zA-Z]+\/(.+)/', $newFile, $matches, PREG_OFFSET_CAPTURE)) {
									if (isset($matches[1][0])) {
										$newFile = $matches[1][0];
									}
								}
								$item = [
									'path' => $newFile,
								];
								if (isset($pathInfo['extension'])) {
									$item['file'] = $newFile;
									$item['name'] = $pathInfo['basename'];
									unset($item['path']);
								}
								$list[] = $item;
								++$key;
							}
						}
					}
				}
			}

			if ($file['totalFiles']) {
				$key = 0;
				foreach ($list as $index => $item) {
					foreach ($item as $param => $value) {
						switch ($param) {
							case 'file':
								$path = $item['file'];
								$context['list'][$index] = sprintf('%s%s', $context['download_prefix'], $path);
								$context['files'][$index]['path'] = sprintf('%s%s', $context['download_prefix'], $path);
								$context['files'][$index]['name'] = basename($path);
								break 2;
							case 'id':
								break;
							default:
								$this->fileBucketService->changeStorage($context['working_disk']);
								if ($this->fileBucketService->exists($context['download_prefix'].$value)) {
									$files = $this->fileBucketService->listContents($context['download_prefix'].$value, true);
									if (null !== $files) {
										foreach ($files as $fIndex => $file) {
											$baseName = basename($file['path']);
											$context['list'][$key] = $file;
											$context['files'][$key]['path'] = $file['path'];
											$context['files'][$key]['name'] = $baseName;
											if ('path' !== $param) {
												$context['files'][$key]['prefix'] = sprintf('%s/%s/', $item['id'], $param);
											}
											++$key;
										}
									}
								}
								break;
						}
					}
				}
				if (isset($context['list']) && is_array($context['list']) && count($context['list'])) {
					$context['statistics']['totalFiles'] = count($context['list']);
					$context['statistics']['processedFiles'] = 0;
					$this->loggerSrv->addInfo("{$history->getName()}: {$context['statistics']['totalFiles']} files from the list");
				}
			}
			if (!$content) {
				$this->loggerSrv->addError(sprintf('unable to find file named: %s', $path));

				return;
			}
			if ($wf->can($history, 'prepared')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepared');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
