<?php

/*
 *  - MapperContent-Action -
 *
 *  This action will process the information/results coming from
 *  an OCR process to find some specific incidents.
 *
 *  Important!: This Action will use the OCR content of the files,
 *  so the Action OcrAzure or OcrTika should be executed first.
 *
 *  -> Inputs:
 *    - filesList: List of files with their OCR content.
 *    - mappings: The mappings to find the incidents.
 *
 *
 *  -> Outputs:
 *    - documentsResult: The result of the mapping process.
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Service\RegexService;
use App\Workflow\HelperServices\EmailParsingService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MapperContentAction extends Action
{
	public const ACTION_DESCRIPTION = 'Mapper specific content from files';
	public const ACTION_INPUTS = [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files with their OCR content.',
		],
		'mappings' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'The mappings to find the incidents.',
		],
	];

	public const ACTION_OUTPUTS = [
		'documentsResult' => [
			'description' => 'The result of the mapping process.',
			'type' => 'array',
		],
		'filesError' => [
			'description' => 'List of files with errors.',
			'type' => 'array',
		],
	];
	private const OCR_TYPE_INTERNAL = 'internal';
	private EmailParsingService $emailParsingSrv;
	private CloudFileSystemService $fileBucketService;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		EmailParsingService $emailParsingSrv,
		MonitorLogService $monitorLogSrv,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->emailParsingSrv = $emailParsingSrv;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'MapperContentAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesList = $this->aux['filesList'];
		$mappings = $this->aux['mappings'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$documentsResult = [];

			$this->fileBucketService->changeStorage(0);

			foreach ($filesList as $info) {
				$docKey = null;
				$content = null;
				$language = null;
				$project = null;
				$ocrType = null;

				if (!isset($info['ocr'])) {
					continue;
				}

				foreach ($info['ocr'] as $ocr) {
					$docKey = $ocr['name'];
					$content = $this->fileBucketService->download($ocr['path']);
					$language = $ocr['language'];
					$project = $ocr['project'];
					$ocrType = $ocr['ocr'];
				}

				$documentsResult[$docKey] = [
					'file' => [],
					'language' => $language,
					'project' => $project,
				];

				if (is_array($content)) {
					$content = array_shift($content);
				}
				$data = explode(PHP_EOL, $content);

				foreach ($mappings as $mappingKey => $mapping) {
					foreach ($data as $splittedText) {
						$matches = [];
						$content = $this->emailParsingSrv->cleanText($splittedText);
						$content = str_replace(['<p>', '</p>', ')'], '', $content);
						$content = trim($content);
						$this->emailParsingSrv->initMappings($mappings);
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

				if (empty($documentsResult[$docKey]['file']['type']) && self::OCR_TYPE_INTERNAL === $ocrType) {
					$this->sendErrorMessage(
						"[FLOW]:  Unable to map completely {$documentsResult[$docKey]['file']} for FLOW",
						[
							'reason' => 'mapping_error',
							'file' => $docKey,
						],
						null,
						null
					);
					$filesError[] = $docKey;
					$this->loggerSrv->addWarning();
				}
			}

			if (!count($documentsResult)) {
				$msg = '[FLOW]: Unable to get any information from files for Attestation workflow.';
				$this->sendErrorMessage(
					$msg,
					[
						'reason' => 'mapping_error',
					],
					null,
					null
				);
				throw new BadRequestHttpException($msg);
			}

			$this->outputs = [
				'documentsResult' => $documentsResult,
				'filesError' => $filesError ?? 0,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendStartMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
