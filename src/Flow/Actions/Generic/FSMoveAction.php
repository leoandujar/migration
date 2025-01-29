<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class FSMoveAction extends Action
{
	public const ACTION_DESCRIPTION = 'Move files to a new directory on the file system';
	public const ACTION_INPUTS = [
		'filesUnZipped' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to move to a new directory.',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesUnZipped' => [
			'description' => 'List of files moved to a new directory.',
			'type' => 'array',
		],
	];

	private FileSystemService $fileSystemSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->actionName = 'FSMoveAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesUnZipped = $this->aux['filesUnZipped'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$identifier = '/files_to_upload/'.(new \DateTime())->format('Y_m_d_H_i_s');
			$toUploadPath = $this->fileSystemSrv->filesPath.$identifier;
			$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $identifier);
			foreach ($filesUnZipped as $index => $file) {
				$localPath = $file['pathLocalFS'] ?? $file['path'];
				$fileWithin = $this->fileSystemSrv->getContentDir($localPath);
				$truePath = $localPath."/$fileWithin[2]";
				$content = file_get_contents($truePath);
				$filename = pathinfo($truePath, PATHINFO_BASENAME);
				$filename = pathinfo($filename, PATHINFO_FILENAME);
				$extension = pathinfo($truePath, PATHINFO_EXTENSION);
				$newFilePath = $toUploadPath."/RETURN_TRANSLATION_{$filename}_LANGUAGE.$extension";
				$this->fileSystemSrv->createOrOverrideFile($newFilePath, $content);
				$filesUnZipped[$index]['pathLocalFS'] = $newFilePath;
			}

			$this->outputs = [
				'filesUnZipped' => $filesUnZipped,
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
