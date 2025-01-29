<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class BucketUploadAction extends Action
{
	public const ACTION_DESCRIPTION = 'Upload files to a bucket.';
	public const ACTION_INPUTS = [
		'zipPath' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'string',
			'details' => 'If a Zip generator Action is behind this one, the zipPath will be set automatically.',
			'description' => 'Path to the zip file to upload.',
			'canReplacedFor' => 'bool',
		],
		'donePath' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'string',
			'details' => 'NOT USED FOR NOW!. It was only for KP Custom Action that currently is stopped its develop.',
			'description' => 'Path to the done file to upload.',
			'canReplacedFor' => 'bool',
		],
		'fromFolder' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'details' => 'NOT USED FOR NOW!. It was only for KP Custom Action that currently is stopped its develop.',
		],
		'destinationFolder' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'Destination folder in the bucket.',
		],
		'targetBucket' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'Bucket to upload files.',
		],
		'filesList' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to upload.',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = null;
	private FileSystemService $fileSystemSrv;
	private CloudFileSystemService $fileBucketSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketService,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->fileBucketSrv = $fileBucketService;
		$this->actionName = 'BucketUploadAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$zipPath = $this->aux['zipPath'];
		$donePath = $this->aux['donePath'];
		$fromFolder = $this->aux['fromFolder'];
		$destinationFolder = $this->aux['destinationFolder'];
		$targetBucket = $this->aux['targetBucket'];
		$filesList = $this->aux['filesList'];
		$context = $this->params;

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if ($zipPath && $donePath && $fromFolder) {
				$fileName = basename($zipPath);
				$devFolderName = $context['testMode'] ? $context['testingFolder'] : $context['productionFolder'];
				$remotePath = "$destinationFolder/$devFolderName/$fromFolder/ReturnFiles";
				$this->fileBucketSrv->changeStorage($targetBucket);
				$this->fileBucketSrv->upload("$remotePath/$fileName", $zipPath);
				$fileName = basename($donePath);
				$this->fileBucketSrv->upload("$remotePath/$fileName", $donePath);
			}

			if ($filesList) {
				foreach ($filesList as $file) {
					$fileName = basename($file['pathLocal']);
					$this->fileBucketSrv->changeStorage($targetBucket);
					$this->fileBucketSrv->upload("$destinationFolder/$fileName", $file['pathLocal']);
				}
			}

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
