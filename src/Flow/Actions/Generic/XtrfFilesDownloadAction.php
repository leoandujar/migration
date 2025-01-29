<?php

namespace App\Flow\Actions\Generic;

use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class XtrfFilesDownloadAction extends Action
{
	public const ACTION_DESCRIPTION = 'Download files from Xtrf';
	public const ACTION_INPUTS = [
		'xtrfProjectId' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'Xtrf Project Id.',
		],
		'xtrfContactPersonId' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer|string',
			'description' => 'Xtrf Contact Person Id.',
		],
	];

	public const ACTION_OUTPUTS = [
		'zipFilePath' => [
			'description' => 'Path to the zip file.',
			'type' => 'string',
		],
		'filesList' => [
			'description' => 'Overwrite List of files downloaded adding key.',
			'type' => 'array',
		],
	];
	private FileSystemService $fileSystemSrv;
	private CustomerPortalConnector $portalConn;
	private XtrfQuoteService $xtrfQuoteSrv;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		CustomerPortalConnector $portalConn,
		XtrfQuoteService $xtrfQuoteSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->portalConn = $portalConn;
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->actionName = 'XtrfFilesDownloadAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->sendStartMessage();

		$this->getInputs();
		$xtrfProjectId = $this->aux['xtrfProjectId'];
		$xtrfContactPersonId = $this->aux['xtrfContactPersonId'];

		try {
			$this->setMonitorObject();

			$xtrfSessionId = $this->xtrfQuoteSrv->xtrfLoginWithToken($xtrfContactPersonId);
			$projectFilesResponse = $this->portalConn->projectDownloadOutputFiles($xtrfProjectId, $xtrfSessionId);
			if (!$projectFilesResponse->isSuccessfull()) {
				$msg = "Unable to download files for project $xtrfProjectId from Xtrf.";
				$this->loggerSrv->addError($msg);
				$this->monitorLogSrv->appendError([
					'message' => $msg,
				]);
				throw new BadRequestHttpException($msg);
			}

			$rootPath = "xtrf_trans_files/$xtrfProjectId";
			$zipFilePath = "{$this->fileSystemSrv->filesPath}/$rootPath/$xtrfProjectId.zip";
			$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, $rootPath);
			$this->fileSystemSrv->createOrOverrideFile($zipFilePath, $projectFilesResponse->getRaw());

			$context['zip_root_path'] = $rootPath;
			$context['zip_file_path'] = $zipFilePath;

			$filesList[] = [
				'name' => "$xtrfProjectId.zip",
				'pathLocalFS' => $zipFilePath,
				'extension' => 'zip',
			];

			$this->outputs = [
				'zipFilePath' => $zipFilePath,
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
