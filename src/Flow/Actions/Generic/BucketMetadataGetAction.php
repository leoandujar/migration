<?php

/*
 *  - GetFilesInfoBucket-ACTION -
 *
 *  Based on the paths that have been obtained from EntityProjectsCollectAction, or a string
 *  with a given path, it will search the appropriate bucket and bring relevant information
 *  from the files of such path(s).
 *
 *  -> Inputs:
 *     - sourceDisk: string with the name of the storage to use (As a retrieve content, can work on 3 and 5).
 *     - paths: array (or simple string with a path) with the paths to search the files.
 *
 *  -> Outputs:
 *    - filesList: array with the information of the files found in the given path(s). Like that:
 *
 *                  [
 *                      'path' => $itemPath,
 *                      'name' => $fileName,
 *                      'language' => $folder['language'] ?? null,
 *                      'project' => $folder['project'] ?? null,
 *                  ];
 *
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BucketMetadataGetAction extends Action
{
	public const ACTION_DESCRIPTION = 'Get metadata from files in a bucket.';
	public const ACTION_INPUTS = [
		'sourceDisk' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'Bucket to get files metadata.',
		],
		'paths' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array or string',
			'details' => 'This input can be an array with multiple strings (paths) or a single path as string.',
			'description' => 'List of paths to search files.',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'List of files found in the given path(s).',
			'type' => 'array',
		],
	];

	private CloudFileSystemService $fileBucketService;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		CloudFileSystemService $fileBucketService,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'BucketMetadataGetAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$sourceDisk = $this->aux['sourceDisk'];
		$folders = $this->aux['paths'];

		if (is_string($folders)) {
			$folders = [['path' => $folders]];
		}

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if (!$this->fileBucketService->checkStorage($sourceDisk)) {
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
			$files = [];

			foreach ($folders as $folder) {
				$path = $folder['path'];
				$filesOfFolder = $this->fileBucketService->listContents($path)->toArray();
				foreach ($filesOfFolder as $file) {
					$itemPath = $file['path'];
					$fileName = basename($itemPath);
					$extension = pathinfo($fileName, PATHINFO_EXTENSION);
					$files[] = [
						'path' => $itemPath,
						'name' => $fileName,
						'extension' => $extension,
						'arguments' => ('zip' === $extension)
							? (str_contains($fileName, 'done') ? 'metadata' : 'translations')
							: 'none',
						'language' => $folder['language'] ?? null,
						'project' => $folder['project'] ?? null,
					];
					$this->loggerSrv->addInfo("[FLOW]: BucketMetadataGetAction Adding file: $fileName to array.");
				}
			}

			if (!count($files)) {
				throw new BadRequestHttpException('[FLOW]: No files found in the given path(s).');
			}

			$this->outputs = [
				'filesList' => $files,
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
