<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class UnzipAction extends Action
{
	public const ACTION_DESCRIPTION = 'Unzip files';
	public const ACTION_INPUTS = [
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the files to unzip (with -pathLocalFS- key in array).',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesUnZipped' => [
			'description' => 'List of files unzipped.',
			'type' => 'array',
		],
		'filesList' => [
			'description' => 'Overwrite List of files adding extractedPath key to -filesList-.',
			'type' => 'array',
		],
	];
	private FileSystemService $fileSystemSrv;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->actionName = 'UnzipAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->sendStartMessage();

		$this->getInputs();
		$filesList = $this->aux['filesList'];

		try {
			$this->setMonitorObject();

			$pathsFilesUnZipped = [];
			$filesUnZipped = [];

			foreach ($filesList as $index => $file) {
				if ('zip' !== $file['extension']) {
					continue;
				}
				$localPath = $file['pathLocalFS'];
				$purePath = dirname($localPath);
				$pathfile = $purePath.'/'.pathinfo($file['name'], PATHINFO_FILENAME);
				$finalPathExtracted = $pathfile.'/extracted';
				$this->fileSystemSrv->createDirectory($purePath, $finalPathExtracted);
				$wasUnzipped = $this->fileSystemSrv->unzipFile($localPath, $finalPathExtracted);
				if (!$wasUnzipped) {
					$this->sendErrorMessage(
						"[FLOW]: Error unzipping file: $localPath.",
						[
							'reason' => 'unzip_error',
							'message' => 'Error unzipping file',
						],
						null,
						null
					);

					return self::ACTION_STATUS_ERROR;
				}
				$pathsFilesUnZipped[] = $finalPathExtracted;
				$filesList[$index]['extractedPath'] = $finalPathExtracted;
			}

			foreach ($pathsFilesUnZipped as $path) {
				$filesInFolder = $this->fileSystemSrv->getContentDir($path);
				foreach ($filesInFolder as $file) {
					$filesUnZipped[] = [
						'path' => $path.'/'.$file,
						'name' => $file,
					];
				}
			}

			$this->outputs = [
				'filesUnZipped' => $filesUnZipped,
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
