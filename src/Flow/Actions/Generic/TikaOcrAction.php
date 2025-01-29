<?php

/*
 *  - OcrTika-Action -
 *
 *  Perform OCR process through Tika. Tika needs per file his 'path' updated for Download Action.
 *  So you should run BucketFilesDownloadAction before this action.
 *  If Tika fails, it will try to perform OCR through Azure Cognitive.
 *
 *   -> Inputs:
 *      - filesList: array with the files to perform OCR (with 'pathInFS' key in array).
 *      - azureModelId: string with the azure model id.
 *
 *   -> Outputs:
 *      - filesList: array with the path (bucket local) to perform OCR (with 'ocr' key in array).
 *      - filesOcr: integer with the total of files that were OCR (notify needs it).
 *      - filesError: integer with the total of files that were not OCR (notify needs it).
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\ApacheTika\TikaConnector;
use App\Connector\AzureCognitive\AzureVisionConnector;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class TikaOcrAction extends Action
{
	private const AZURE_MODEL_PREBUILT_READ = 'prebuilt-read';
	public const ACTION_DESCRIPTION = 'Does OCR on files using Tika and Azure Cognitive if fails';
	public const ACTION_INPUTS =  [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the files to perform OCR (with -pathLocal- key in array).',
			'canReplacedFor' => 'files',
		],
		'azureModelId' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				self::AZURE_MODEL_PREBUILT_READ,
			],
			'description' => 'String with the azure model id.',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'Overwrite -filesList- with OCR content adding key.',
			'type' => 'array',
		],
		'filesOcr' => [
			'description' => 'Number of files OCR.',
			'type' => 'integer',
		],
		'filesError' => [
			'description' => 'Number of files with OCR error.',
			'type' => 'integer',
		],
	];
	private TikaConnector $tikaConn;
	private CloudFileSystemService $fileBucketService;
	private AzureVisionConnector $azureVisionConn;

	public function __construct(
		AzureVisionConnector $azureVisionConn,
		TikaConnector $tikaConn,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->tikaConn = $tikaConn;
		$this->azureVisionConn = $azureVisionConn;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'TikaOcrAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesList = $this->aux['filesList'];
		$azureModelId = $this->aux['azureModelId'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$ocrType = null;

			foreach ($filesList as $index => $info) {
				$filename = $info['name'];
				$filePath = $info['pathLocal'];
				$cloudPath = "genericWorkflowData/tikaOcr/$filename";

				$this->loggerSrv->addInfo("[FLOW]: Processing file in Tika for file: $filePath");
				$contentResponse = $this->tikaConn->getFileContent($filePath, null);
				if (!$contentResponse || !$contentResponse->isSuccessfull()) {
					$this->sendErrorMessage(
						"[FLOW]: Skipping: Unable to get file content from tika for attestation workflow: $filePath. Trying with Azure",
						[
							'reason' => 'internal_ocr_error',
							'file' => $filename,
						],
						null,
						null
					);

					$filesOcr[] = $filename;
					$this->loggerSrv->addInfo("Processing file in Azure Cognitive for file: $filePath");
					$this->fileBucketService->changeStorage(5);
					if ($this->fileBucketService->upload($cloudPath, $filePath)) {
						$url = $this->fileBucketService->getTemporaryUrl($cloudPath, 1);
						$analyzeResponse = $this->azureVisionConn->analyzeDocument($url, '1-3', $azureModelId);
						if (empty($analyzeResponse) || !$analyzeResponse->isSuccessfull()) {
							$this->sendErrorMessage(
								"[FLOW]: Unable to get file content from Azure Cognitive for: $filePath",
								[
									'reason' => 'external_ocr_error',
									'file' => $filename,
								],
								null,
								null
							);
							$filesError[] = $filename;
							continue;
						}
						$responseData = $analyzeResponse->getData();
						$content = $responseData['analyzeResult']['content'];
						$ocrType = 'azure';
						$this->loggerSrv->addInfo('[GEN-WF]: (Tika fail). Azure Cognitive OCR successfull!');
					}
				} else {
					$content = $contentResponse->getRaw();
					$ocrType = 'internal';
				}

				$this->fileBucketService->changeStorage(0);
				$this->fileBucketService->write($cloudPath, $content);

				$filesList[$index]['ocr'][] = [
					'name' => $filename,
					'path' => $cloudPath,
					'pathLocal' => $cloudPath,
					'language' => $info['language'] ?? null,
					'project' => $info['project'] ?? null,
					'ocr' => $ocrType,
				];
			}

			$this->outputs = [
				'filesOcr' => $filesOcr ?? 0,
				'filesError' => $filesError ?? 0,
				'filesList' => $filesList,
			];

			$this->setOutputs();

			$this->outputs = [];

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
