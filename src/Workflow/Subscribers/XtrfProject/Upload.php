<?php

namespace App\Workflow\Subscribers\XtrfProject;

use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Service\FileSystem\CloudFileSystemService;
use App\Connector\Ocr\OcrConnector;
use App\Service\Xtrf\XtrfQuoteService;
use App\Connector\Xtrf\XtrfConnector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Workflow\Services\XtrfProject\Start;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Upload implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private CloudFileSystemService $fileBucketService;
	private OcrConnector $connector;
	private KernelInterface $kernel;
	private EntityManagerInterface $em;
	private CustomerPortalConnector $customerPortalConnector;
	private XtrfConnector $xtrfConnector;
	private XtrfQuoteService $xtrfQuoteSrv;

	/**
	 * Upload constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		XtrfConnector $xtrfConnector,
		CloudFileSystemService $fileBucketService,
		OcrConnector $connector,
		KernelInterface $kernel,
		XtrfQuoteService $xtrfQuoteSrv,
		CustomerPortalConnector $customerPortalConnector,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->fileBucketService = $fileBucketService;
		$this->connector = $connector;
		$this->kernel = $kernel;
		$this->xtrfConnector = $xtrfConnector;
		$this->customerPortalConnector = $customerPortalConnector;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project.completed.downloaded' => 'uploadFiles',
		];
	}

	public function uploadFiles(Event $event)
	{
		try {
			$history = $event->getSubject();
			$wf = $this->registry->get($history, 'xtrf_project');
			$context = $history->getContext();
			$path = sprintf('%s/var/%s', $this->kernel->getProjectDir(), $event->getWorkflowName());
			if (!is_dir($path)) {
				mkdir($path);
			}
			$files = $context['ready_files'] ?: $context['files'];
			$this->fileBucketService->changeStorage($context['source_disk']);
			$filesTrace = [];
			foreach ($files as $index => $fileContent) {
				$filesTrace[$index]['path'] = $fileContent['path'];
				$file = $this->fileBucketService->download($fileContent['path']);
				$fileName = basename($fileContent['path']);
				$filesTrace[$index]['filename'] = $fileName;
				$response = null;
				switch ($context['type']) {
					case Start::TYPE_PROJECT:
						// Add file extension
						$response = $this->xtrfConnector->uploadProjectFile([[
							'name' => 'file',
							'contents' => $file,
							'filename' => $fileName,
						]]);
						if (!$response->isSuccessfull()) {
							$this->loggerSrv->addError('Error uploading files for WF Project', [
								'message' => $response->getErrorMessage(),
							]);
							throw new BadRequestException($response->getErrorMessage());
						}
						$filesTrace[$index]['token'] = $response->getToken();
						break;
					case Start::TYPE_QUOTE:
						$response = $this->customerPortalConnector->uploadProjectFile([[
							'name' => 'file',
							'contents' => $file,
							'filename' => $fileName,
						]], $context['sessionID']);
						$result = $response->getResult();
						if (isset($result['id'])) {
							$filesTrace[$index]['token'] = $result['id'];
						}
						break;
				}
				if (null === $response) {
					$this->loggerSrv->addWarning(sprintf('the file %s was not loaded', $fileName));
					continue;
				}
				$handlerPath = sprintf('%s/%s', $path, $fileName);
				if (isset($context['ocr_active']) && $context['ocr_active']) {
					$handler = fopen($handlerPath, 'a+');
					fwrite($handler, $file);
					fclose($handler);
					$mimeType = mime_content_type($handlerPath);
					switch ($mimeType) {
						case 'application/pdf':
						case 'application/msword':
						case 'image/tiff':
						case 'image/jpeg':
						case 'image/bmp':
						case 'image/png':
							$response = $this->connector->send($handlerPath, $context['ocr_languages'] ?: 'english,german,spanish', $context['ocr_output_format'] ?: 'pdf,txt');
							if (count($response)) {
								foreach ($response as $name => $content) {
									$name = sprintf('%s_ocr_%s', $fileName, $name);
									switch ($context['type']) {
										case Start::TYPE_PROJECT:
											$response = $this->xtrfConnector->uploadProjectFile([[
												'name' => 'file',
												'contents' => $content,
												'filename' => $name,
											]]);
											$context['files'][$index]['ocr_file'][] = [
												'name' => $name,
												'token' => $response->getToken(),
											];
											break;
										case Start::TYPE_QUOTE:
											if (!isset($context['auth_token'])) {
												$context['auth_token'] = $this->xtrfQuoteSrv->xtrfGetRawToken(
													$context['template']['contact_person']
												);
											}
											$response = $this->customerPortalConnector->uploadProjectFile([[
												'name' => 'file',
												'contents' => $content,
												'filename' => $name,
											]], $context['sessionID']);
											$result = $response->getResult();
											$filesTrace[$index]['ocr_file'][] = [
												'name' => $name,
												'id' => $result['id'],
											];
											break;
									}
								}
							}
							unlink($handlerPath);
							break;
						default:
							unlink($handlerPath);
					}
				}
				if (isset($fileContent['path'])) {
					$this->fileBucketService->deleteFile($fileContent['path']);
				}
			}
			rmdir($path);
			$context['files'] = $filesTrace;
			if ($wf->can($history, 'published')) {
				if ($history instanceof WFHistory) {
					$history->setContext($context);
					if (!$this->em->isOpen()) {
						$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
					}
					$this->em->persist($history);
					$this->em->flush();
				}
				$wf->apply($history, 'published');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
