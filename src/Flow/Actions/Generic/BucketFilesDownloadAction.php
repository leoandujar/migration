<?php

/*
 *  - DownloadFromBucket-Action
 *
 *  This action downloads files from a bucket.
 *  It used to be called "Retrieve", but it included more steps,
 *  now it is limited to downloading a file from the Buckets.
 *  Can work on 3 and 5. This Action NO MORE adds a 'content' key to the filesList.
 *
 *
 *  -> Inputs:
 *    - sourceDisk: bucket to download file.
 *    - filesList: list of files to download. This is produced by
 *      "BucketMetadataGetAction".
 *
 *  -> Outputs:
 *    - filesList: list of files with pathLocal key.
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileSystem\FileSystemService;

class BucketFilesDownloadAction extends Action
{
	public const ACTION_DESCRIPTION = 'Download files from a bucket.';
	public const ACTION_INPUTS = [
		'sourceDisk' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'Bucket to download files.',
		],
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to download. This is produced by "BucketMetadataGetAction".',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'Download files in server disk, adding a key with pathLocal key.',
			'type' => 'array',
		],
	];

	private CloudFileSystemService $fileBucketService;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		CloudFileSystemService $fileBucketService,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileBucketService = $fileBucketService;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->actionName = 'BucketFilesDownloadAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$sourceDisk = $this->aux['sourceDisk'];
		$filesList = $this->aux['filesList'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if (!$this->fileBucketService->checkStorage($sourceDisk)) {
				$this->loggerSrv->addInfo("[FLOW]: Storage: $sourceDisk, not exists.");
				$this->sendErrorMessage(
					"[FLOW]: Storage: $sourceDisk, not exists.",
					[
						'reason' => 'storage_not_exists',
						'message' => 'Storage not exists',
					],
					null,
					null
				);

				return self::ACTION_STATUS_ERROR;
			}

			$this->fileBucketService->changeStorage($sourceDisk);

			foreach ($filesList as $index => $file) {
				$itemPath = mb_convert_encoding($file['path'], 'UTF-8', 'UTF-8');
				$fileName = basename($itemPath);
				$file = $this->fileBucketService->download($itemPath);
				$this->fileBucketService->changeStorage(0);
				$localPath = 'flow/localFiles/'.date('d-m-Y-H:i')."/$fileName";
				$this->fileBucketService->write($localPath, $file);
				$this->loggerSrv->addInfo("[FLOW]: File: $fileName, downloaded in local bucket in $localPath.");
				$filesList[$index]['pathLocal'] = $localPath;
				$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'localFile');
				$filesList[$index]['pathLocalFS'] = $this->fileSystemSrv->filesPath."/localFile/$fileName";
				$this->fileSystemSrv->createOrOverrideFile($filesList[$index]['pathLocalFS'], $file);
				$this->fileBucketService->changeStorage($sourceDisk);
			}

			$this->outputs = [
				'filesList' => $filesList,
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
