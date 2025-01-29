<?php

/*
 *  - Azure OCR - Action -
 *
 *  This action obtains the content of an OCR process on a file.
 *
 *  Important!: To run an OCR process through Azure, you first need
 *  to obtain the TemporaryUrl of each file. So you should first run
 *  BucketUrlGenerateAction before this.
 *
 *  -> Inputs:
 *    - filesList: array with the files to be processed (needs 'temporaryUrl' key).
 *    - azureModelId: string with the id of the Azure model.
 *    - pages: integer with the number of pages to be processed (default null).
 *
 *  -> Outputs:
 *   - filesList: array with the files processed and their path in local Bucket. Like that:
 *
 *      $filesList[$index]['ocr'][] = [
 *                  'name' => $fileName,
 *                  'path' => $cloudPath,
 *                  'project' => $project ?? null,
 *                  'language' => $language ?? null,
 *                  'ocr' => 'azure',
 *             ];
 *
 *   - filesOcr: integer with the number of files processed (Notify uses that).
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\AzureCognitive\AzureVisionConnector;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class AzureOcrAction extends Action
{
	private const AZURE_MODEL_PREBUILT_READ = 'prebuilt-read';
	public const ACTION_DESCRIPTION = 'Does OCR on files using Azure Cognitive Services.';
	public const ACTION_INPUTS = [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to be processed.',
			'canReplacedFor' => 'files',
		],
		'azureModelId' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				self::AZURE_MODEL_PREBUILT_READ,
			],
			'description' => 'The Azure model to be used.',
		],
		'pages' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'Number of pages to be processed (default 3).',
			'canReplacedFor' => 'bool',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'Add to a -filesList- it\'s OCR content as a subfield for each file.',
			'type' => 'array',
		],
		'filesOcr' => [
			'description' => 'Number of files that have been OCR processed.',
			'type' => 'integer',
		],
	];

	private AzureVisionConnector $azureVisionConn;
	private CloudFileSystemService $fileBucketService;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
		AzureVisionConnector $azureVisionConn,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->azureVisionConn = $azureVisionConn;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'AzureOcrAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();

		$filesList = $this->aux['filesList'];
		$azureModelId = $this->aux['azureModelId'];
		$pages = $this->aux['pages'] ?? null;

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$filesOcr = [];

			foreach ($filesList as $index => $file) {
				$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
				if (in_array($extension, ['docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx'])) {
					continue;
				}

				$fileName = $file['name'];
				$temporaryUrl = $file['temporaryUrl'];
				$language = $file['language'] ?? null;
				$project = $file['project'] ?? null;

				$cloudPath = "genericWorkflowData/azureOcr/$fileName";

				$analyzeResponse = $this->azureVisionConn->analyzeDocument($temporaryUrl, $pages, $azureModelId);

				if (empty($analyzeResponse) || !$analyzeResponse->isSuccessfull()) {
					$this->sendErrorMessage(
						"[FLOW]: Unable to get file content from Azure Cognitive: $fileName",
						[
							'reason' => 'external_ocr_error',
							'file' => $fileName,
						],
						null,
						null
					);
					continue;
				}

				$responseData = $analyzeResponse->getData();
				$content = $responseData['analyzeResult']['content'];

				$this->fileBucketService->changeStorage(0);
				$this->fileBucketService->write($cloudPath, $content);

				$filesList[$index]['ocr'][] = [
					'name' => $fileName,
					'path' => $cloudPath,
					'project' => $project ?? null,
					'language' => $language ?? null,
					'ocr' => 'azure',
				];
				$filesOcr[] = $fileName;
				$this->loggerSrv->addInfo("[FLOW]: Azure Ocr content has been added to the file: $fileName.");
			}

			$this->outputs = [
				'filesList' => $filesList,
				'filesOcr' => count($filesOcr),
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
