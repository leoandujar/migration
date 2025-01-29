<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\Xtrf\XtrfConnector;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class XtrfFileUploadAction extends Action
{
	public const ACTION_DESCRIPTION = 'Upload files to XTRF';
	public const ACTION_INPUTS = [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the files to upload to XTRF (with -pathLocal- key in array).',
			'canReplacedFor' => 'files',
		],
		'fromFs' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'bool',
			'description' => 'Boolean to indicate if the files are from FS.',
		],
		'filesTaskMapping' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the files to upload to XTRF (with -path- key in array).',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'Overwritte List of files uploaded to XTRF adding token key.',
			'type' => 'array',
		],
	];

	private XtrfConnector $xtrfConnector;
	private CloudFileSystemService $fileBucketService;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		XtrfConnector $xtrfConnector,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->xtrfConnector = $xtrfConnector;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'XtrfFileUploadAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesList = $this->aux['filesList'] ?? null;
		$filesTaskMapping = $this->aux['filesTaskMapping'];
		$fromFS = $this->aux['fromFs'] ?? false;

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if ($fromFS) {
				foreach ($filesTaskMapping as $index => $file) {
					$result = $this->xtrfUploaderFromFS($file);
					if (null === $result) {
						throw new BadRequestHttpException('[FLOW]: Error uploading files to XTRF');
					}
					$filesTaskMapping[$index]['token'] = $result;
				}

				$this->outputs = [
					'filesTaskMapping' => $filesTaskMapping,
				];
			} else {
				foreach ($filesList as $index => $file) {
					$result = $this->xtrfUploader($file);
					if (null === $result) {
						throw new BadRequestHttpException('[FLOW]: Error uploading files to XTRF');
					}
					$filesList[$index]['token'] = $result;
					if (isset($file['ocr'])) {
						foreach ($file['ocr'] as $key => $ocr) {
							$result = $this->xtrfUploader($ocr);
							if (null === $result) {
								throw new BadRequestHttpException('[FLOW]: Error uploading files to XTRF');
							}
							$filesList[$index]['ocr'][$key]['token'] = $result;
						}
					}
				}

				$this->outputs = [
					'filesList' => $filesList,
				];
			}

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}

	private function xtrfUploader($file): ?string
	{
		$this->fileBucketService->changeStorage(0);
		$response = $this->xtrfConnector->uploadProjectFile([[
			'name' => 'file',
			'contents' => $this->fileBucketService->download($file['pathLocal']),
			'filename' => $file['name'],
		]]);
		if (!$response || !$response->isSuccessfull()) {
			$message = $response?->getErrorMessage();
			$this->sendErrorMessage(
				$message,
				[
					'message' => $message,
					'reason' => 'upload_file',
					'file' => $file['name'],
				],
				null,
				null
			);

			return null;
		}
		$this->loggerSrv->addInfo("[GEN-WF]: Uploaded to xtrf successfully! {$file['name']}.");

		return $response->getToken();
	}

	private function xtrfUploaderFromFS($file): ?string
	{
		$response = $this->xtrfConnector->uploadProjectFile([[
			'name' => 'file',
			'contents' => file_get_contents($file['path']),
			'filename' => $file['filename'],
		]]);

		if (!$response->isSuccessfull()) {
			$message = $response?->getErrorMessage();
			$this->sendErrorMessage(
				$message,
				[
					'message' => $message,
					'reason' => 'upload_file',
					'file' => $file['name'],
				],
				null,
				null
			);

			return null;
		}

		return $response->getToken();
	}
}
