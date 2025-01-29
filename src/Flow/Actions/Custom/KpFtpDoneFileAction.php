<?php

namespace App\Flow\Actions\Custom;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class KpFtpDoneFileAction extends Action
{
	public const ACTION_DESCRIPTION = 'Create a done file for the FTP';
	public const ACTION_INPUTS = [
		'orderNumber' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The order number for the project',
		],
		'valueNumber' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'string',
			'description' => 'The value number for the project',
		],
		'globalDate' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'string',
			'description' => 'The global date for the project',
		],
	];

	public const ACTION_OUTPUTS = [
		'donePath' => [
			'description' => 'Path of the done file',
			'type' => 'string',
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
		$this->actionName = 'KpFtpDoneFileAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$orderNumber = $this->aux['orderNumber'];
		$valueNumber = $this->aux['valueNumber'];
		$globalDate = $this->aux['globalDate'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$doneName = "RETURN_TRANSLATION_{$orderNumber}_{$valueNumber}_{$globalDate}_order_PSP.done";
			$this->fileSystemSrv->createDirectory("{$this->fileSystemSrv->filesPath}", 'temp');
			$donePath = "{$this->fileSystemSrv->filesPath}/temp/$doneName";

			$this->fileSystemSrv->createOrOverrideFile($donePath, '');

			$this->outputs = [
				'donePath' => $donePath,
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
