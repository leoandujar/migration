<?php

namespace App\Workflow\Subscribers\Attestation;

use App\Service\RegexService;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Model\Entity\AVWorkflowMonitor;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Connector\ApacheTika\TikaConnector;
use App\Workflow\HelperServices\MonitorLogService;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Workflow\HelperServices\EmailParsingService;
use App\Service\FileSystem\CloudFileSystemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Connector\AzureCognitive\AzureVisionConnector;

class GetFilesContent implements EventSubscriberInterface
{
	private const OCR_TYPE_INTERNAL = 'internal';
	private const OCR_TYPE_AZURE = 'azure';

	private Registry $registry;
	private TikaConnector $tikaConn;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private EmailParsingService $emailParsingSrv;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private CloudFileSystemService $fileBucketService;
	private AzureVisionConnector $azureVisionConn;

	/**
	 * Prepare constructor.
	 */
	public function __construct(
		Registry $registry,
		AzureVisionConnector $azureVisionConn,
		TikaConnector $tikaConn,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		EmailParsingService $emailParsingSrv,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		CloudFileSystemService $fileBucketService
	) {
		$this->em = $em;
		$this->tikaConn = $tikaConn;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->emailParsingSrv = $emailParsingSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_ATTESTATION);
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->fileBucketService = $fileBucketService;
		$this->azureVisionConn = $azureVisionConn;

		$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_WORKFLOW);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.attestation.completed.download' => 'getFilesContent',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function getFilesContent(Event $event)
	{
		try {
			$this->loggerSrv->addInfo('Starting analyzing the files for Attestation filtered projects.');
			/** @var WFHistory $history */
			$history = $event->getSubject();
			$context = $history->getContext();
			$params = $context['params'];
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
			$fileList = $context['fileList'];
			unset($context['filesLocalPath']);
			$filesContentList = [];
			$filesError = [];
			$filesOcr = [];
			$ocrType = $params['ocrType'];
			if (self::OCR_TYPE_AZURE === $ocrType) {
				foreach ($fileList as $info) {
					$filename = $info['name'];
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					$temporaryUrl = $info['temporaryUrl'];
					$language = $info['language'];
					$project = $info['project'];
					$pages = '1-3';

					if (in_array($extension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])) {
						$pages = null;
					}

					$filesOcr[] = $filename;
					$this->loggerSrv->addInfo("Processing file in Azure Cognitive for file: $filename");

					$analyzeResponse = $this->azureVisionConn->analyzeDocument($temporaryUrl, $pages, $params['azureVisionLanguage']['modelId']);
					if (empty($analyzeResponse) || !$analyzeResponse->isSuccessfull()) {
						$this->monitorLogSrv->appendError([
							'reason' => 'external_ocr_error',
							'file' => $filename,
						]);
						$filesError[] = $filename;
						$this->loggerSrv->addError("Unable to get file content from Azure Cognitive for attestation workflow: $filename");
						continue;
					}
					$responseData = $analyzeResponse->getData();
					$content = $responseData['analyzeResult']['content'];
					$filesContentList[] = [
						'file' => [$filename => $content],
						'language' => $language,
						'project' => $project,
						'ocr' => $ocrType,
					];
				}
			} else {
				foreach ($fileList as $info) {
					$filename = key($info['file']);
					$filePath = $info['file'][$filename];
					$language = $info['language'];
					$project = $info['project'];
					$this->loggerSrv->addInfo("Processing file in Tika for file: $filePath");
					$contentResponse = $this->tikaConn->getFileContent($filePath, null);
					if (!$contentResponse || !$contentResponse->isSuccessfull()) {
						$this->loggerSrv->addWarning("Skipping: Unable to get file content from tika for attestation workflow: $filePath");
						$this->monitorLogSrv->appendError([
							'reason' => 'internal_ocr_error',
							'file' => $filename,
						]);

						$filesOcr[] = $filename;
						$this->loggerSrv->addInfo("Processing file in Azure Cognitive for file: $filePath");
						$cloudPath = "attestation/$project/$filename";
						if ($this->fileBucketService->upload($cloudPath, $filePath)) {
							$url = $this->fileBucketService->getTemporaryUrl($cloudPath, 1);
							$analyzeResponse = $this->azureVisionConn->analyzeDocument($url, '1-3', $params['azureVisionLanguage']['modelId']);
							if (empty($analyzeResponse) || !$analyzeResponse->isSuccessfull()) {
								$this->monitorLogSrv->appendError([
									'reason' => 'external_ocr_error',
									'file' => $filename,
								]);
								$filesError[] = $filename;
								$this->loggerSrv->addError("Unable to get file content from Azure Cognitive for attestation workflow: $filePath");
								continue;
							}
							$responseData = $analyzeResponse->getData();
							$content = $responseData['analyzeResult']['content'];
						}
					} else {
						$content = $contentResponse->getRaw();
					}
					$filesContentList[] = [
						'file' => [$filename => $content],
						'path' => $filePath,
						'language' => $language,
						'project' => $project,
						'ocr' => $ocrType,
					];
				}
			}

			$documentsResult = [];
			foreach ($filesContentList as $info) {
				$docKey = key($info['file']);
				$content = $info['file'][$docKey];
				$language = $info['language'];
				$project = $info['project'];
				$documentsResult[$docKey] = [
					'file' => [],
					'language' => $language,
					'project' => $project,
				];
				if (is_array($content)) {
					$content = array_shift($content);
				}
				$data = explode(PHP_EOL, $content);

				foreach ($context['mapping'] as $mappingKey => $mapping) {
					foreach ($data as $splittedText) {
						$matches = [];
						$content = $this->emailParsingSrv->cleanText($splittedText);
						$content = str_replace(['<p>', '</p>', ')'], '', $content);
						$content = trim($content);
						$this->emailParsingSrv->initMappings($context['mapping']);
						RegexService::match($mapping['pattern_type'], $content, $mapping['pattern'], $matches);
						if (count($matches) >= 2 && !isset($documentsResult[$docKey]['file'][$mappingKey])) {
							if ('type' === $mapping['pattern_key']) {
								$mappingKey = 'type';
							}
							$documentsResult[$docKey]['file'][$mappingKey] = trim($matches[1]);
						} elseif (1 === count($matches)
							&& str_contains(strtolower($matches[0]), strtolower($mapping['label']))
							&& !isset($documentsResult[$docKey]['file'][$mappingKey])) {
							if ('type' === $mapping['pattern_key']) {
								$mappingKey = 'type';
							}
							$documentsResult[$docKey]['file'][$mappingKey] = trim(str_replace(strtolower($mapping['label']), '', strtolower($matches[0])));
						}
					}
				}

				if (empty($documentsResult[$docKey]['file']['type']) && self::OCR_TYPE_INTERNAL === $info['ocr']) {
					$this->monitorLogSrv->appendError([
						'reason' => 'mapping_error',
						'file' => $docKey,
					]);
					$filesError[] = $docKey;
					$this->loggerSrv->addWarning("Unable to map completely {$documentsResult[$docKey]['file']} for attestation workflow");
				}
			}

			if (!count($documentsResult)) {
				$msg = 'Unable to get any information from files for Attestation workflow.';
				$this->loggerSrv->addError($msg);
				throw new BadRequestHttpException($msg);
			}

			$context['filesError'] = array_merge($context['filesError'], $filesError);
			$context['filesOcr'] = $filesOcr;
			$context['documentsResult'] = $documentsResult;

			$wf = $this->registry->get($history, 'attestation');
			if ($wf->can($history, 'get_content')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'get_content');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting files content from Attestation workflow.', $thr);
			throw $thr;
		}
	}
}
