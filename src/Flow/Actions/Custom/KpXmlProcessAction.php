<?php

namespace App\Flow\Actions\Custom;

use App\Flow\Actions\Action;
use App\Flow\Utils\FlowUtils;
use App\Flow\Services\XmlParserService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class KpXmlProcessAction extends Action
{
	public const ACTION_DESCRIPTION = 'Process the XML file with recipients';
	public const ACTION_INPUTS = [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to compare',
		],
		'template' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Template for the project',
		],
		'testMode' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'bool',
			'description' => 'Working on test o prod?',
		],
		'languagesMapping' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Languages mapping for the project',
		],
		'orderNumber' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The order number for the project',
		],
		'batchNumber' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'The batch number for the project',
		],
	];

	public const ACTION_OUTPUTS = [
		'sla' => [
			'description' => 'SLA',
			'type' => 'string',
		],
		'xmlContent' => [
			'description' => 'XML content',
			'type' => 'string',
		],
		'template' => [
			'description' => 'Template',
			'type' => 'array',
		],
		'recipientMap' => [
			'description' => 'Recipient map',
			'type' => 'array',
		],
		'filesTaskMapping' => [
			'description' => 'Files task mapping',
			'type' => 'array',
		],
		'taskCreationMapping' => [
			'description' => 'Task creation mapping',
			'type' => 'array',
		],
		'stats' => [
			'description' => 'Stats',
			'type' => 'array',
		],
		'filesList' => [
			'description' => 'Files list',
			'type' => 'array',
		],
		'translationsPath' => [
			'description' => 'Translations path',
			'type' => 'string',
		],
	];

	private XmlParserService $xmlParserSrv;
	private FileSystemService $fileSystemSrv;
	private FlowUtils $flowUtils;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		XmlParserService $xmlParserSrv,
		FlowUtils $flowUtils,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->xmlParserSrv = $xmlParserSrv;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->flowUtils = $flowUtils;
		$this->actionName = 'KpXmlProcessAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesList = $this->aux['filesList'];
		$template = $this->aux['template'];
		$testMode = $this->aux['testMode'];
		$languages_mapping = $this->aux['languagesMapping'];
		$stats = $this->aux['stats'] ?? [];
		$orderNumber = $this->aux['orderNumber'];
		$batch_number = $this->aux['batchNumber'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$filesProcessed = 0;
			$translationsFilesPath = null;

			foreach ($filesList as $file) {
				$filesExtractedPath = $file['extractedPath'];

				if ('translations' === $file['arguments']) {
					$translationsFilesPath = $filesExtractedPath;
					continue;
				}

				$metadataFileName = $this->fileSystemSrv->findFileByName($filesExtractedPath, 'fulfillment');
				$xmlContent = $this->xmlParserSrv->parse($filesExtractedPath."/$metadataFileName");

				if (!is_array($xmlContent) || empty($xmlContent['data']['recipient'])) {
					$msg = '[FLOW]: Metadata file does not contains recipient information. Unable to continue.';
					$this->sendErrorMessage($msg, [
						'message' => $msg,
					], null, null);
				}

				if (!isset($xmlContent['data']['recipient'][0])) {
					$xmlContent['data']['recipient'] = [$xmlContent['data']['recipient']];
				}

				if (isset($xmlContent['data']['details']['sla'])) {
					$sla = $xmlContent['data']['details']['sla'];
					$template['dates'] = [
						'startDate' => ['time' => (new \DateTime())->getTimestamp() * 1000],
						'deadline' => ['time' => $this->flowUtils->buildDeadLine($sla)],
					];
				}

				$envName = ($testMode) ? 'test' : 'prod';
				$template['name'] = sprintf('%s_Medicare TAF Letters_%s_%s_Batch%s', $envName, date('n.j.y'), $orderNumber, $batch_number);
				$recipients = $xmlContent['data']['recipient'];
				$xmlContent = simplexml_load_file($filesExtractedPath."/$metadataFileName")->asXML();
				$recipientMap = [];
				$languageMapping = $languages_mapping;
				$filesTaskMapping = [];
				$taskCreationMapping = [];
				$stats['forMailing'] = 0;
				$stats['forADA'] = 0;
				$stats['files'] = [];
				$stats['totalFiles'] = 0;

				foreach ($recipients as $recipient) {
					$fileName = $recipient['fileName'];
					++$stats['totalFiles'];
					$recipientTargetLanguage = $recipient['target_language'];
					$languageMappingData = $languageMapping[$recipientTargetLanguage];
					$targetLanguageId = $languageMappingData['language_id'];
					$stats['files'][$recipientTargetLanguage] = ($stats['files'][$recipientTargetLanguage] ?? 0) + 1;
					$service = $languageMappingData['workflows']['default'] ?? $template['serviceId'];
					$vendorMail = $recipient['vendor_mail_flag'] ?? null;
					$vendorAda = $recipient['vendor_ada_flag'] ?? null;

					if ($vendorMail && 'N' !== $vendorMail && isset($languageMappingData['workflows']['vendor_mail_flag'])) {
						++$stats['forMailing'];
						$service = $languageMappingData['workflows']['vendor_mail_flag'];
					}
					if ($vendorAda && 'N' !== $vendorAda && isset($languageMappingData['workflows']['vendor_ada_flag'])) {
						++$stats['forADA'];
						$service = $languageMappingData['workflows']['vendor_ada_flag'];
					}

					$filesTaskMapping[$fileName] = [
						'filename' => $fileName,
						'target_language' => $targetLanguageId,
						'workflow_id' => $service,
						'path' => $translationsFilesPath."/$fileName",
					];
					$taskCreationMapping[$targetLanguageId][] = $service;
					$taskCreationMapping[$targetLanguageId] = array_unique($taskCreationMapping[$targetLanguageId]);
					$recipientMap[$fileName] = false;
				}

				++$filesProcessed;
			}

			if (0 === $filesProcessed) {
				$msg = '[FLOW]: No files processed.';
				$this->sendErrorMessage($msg, [
					'message' => $msg,
				], null, null);
				throw new BadRequestHttpException('[FLOW]: No files processed.');
			}

			$this->outputs = [
				'sla' => $sla,
				'xmlContent' => $xmlContent,
				'template' => $template,
				'recipientMap' => $recipientMap,
				'filesTaskMapping' => $filesTaskMapping,
				'taskCreationMapping' => $taskCreationMapping,
				'stats' => $stats,
				'filesList' => $filesList,
				'translationsPath' => $translationsFilesPath,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage('[FLOW]: Fail parsing and process xml file!', null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
