<?php

namespace App\Flow\Actions\Custom;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class KpFileOrderStatusSendAction extends Action
{
	public const ACTION_DESCRIPTION = 'Sends the order file status';

	public const ACTION_INPUTS =  [
		'feedBackStatus' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'select',
			'options' => ['Recieved', 'Stopped'],
			'description' => 'The status of the feedback',
		],
		'filesTranslated' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'The translated files',
		],
	];

	public const ACTION_OUTPUTS = null;
	private FileSystemService $fileSystemSrv;
	private CloudFileSystemService $fileBucketSrv;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->fileBucketSrv = $fileBucketService;
		$this->actionName = 'KpFileOrderStatusSendAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$feedBackStatus = ($this->aux['feedBackStatus']) ? 'Recieved' : 'Stopped';
		$context = $this->params;
		$totalFiles = count($this->aux['filesTranslated'] ?? $this->aux['filesUnZipped']);

		$context['template'] = $context['templatesLinked'][$context['projectsOrQuotes'][0]];
		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$customerId = $context['template']['customerId'] ?? $context['xtrfCustomerId'];
			$todayDate = date('d-m-Y');
			$devFolderName = $context['testMode'] ? $context['testingFolder'] : $context['productionFolder'];
			$remotePathOrderFile = "/{$context['workingFolder']}/$devFolderName/$customerId/$todayDate/OrderFiles";
			$vendorId = $context['vendorId'];
			$orderId = $context['orderNumber'];
			$date = date('Ymd');
			$levelStatusFilename = "status_{$date}_$vendorId.hdr";
			$dataLevelFileContent = $this->fileBucketSrv->download("$remotePathOrderFile/$levelStatusFilename") ?? '';
			$existingData = [];

			if (!empty($dataLevelFileContent)) {
				$existingRows = explode("\n", trim($dataLevelFileContent));
				foreach ($existingRows as &$row) {
					$columns = explode("\t", $row);
					if ($columns[1] === $orderId && $columns[2] === $feedBackStatus) {
						unset($existingData[$orderId]);
						continue;
					}
					$existingData[] = $row;
				}
			}

			$dataLevelFile = [
				$vendorId, // vendor_id
				$context['orderNumber'], // order_id
				$feedBackStatus, // status
				date('m/d/Y'), // received_date
				'', // shipping_date
				'', // shipping_method
				'', // shipping_cost
				'', // comments
				$totalFiles, // packages_count
				'', // tracking_number
			];
			$existingData[] = implode("\t", $dataLevelFile);
			$localPath = "{$this->fileSystemSrv->filesPath}/return_files/{$context['orderNumber']}/$levelStatusFilename";
			$this->fileSystemSrv->createDirectory("{$this->fileSystemSrv->filesPath}/return_files", $context['orderNumber']);
			$this->fileSystemSrv->createOrOverrideFile($localPath, implode("\n", $existingData));
			$this->fileBucketSrv->changeStorage(CloudFileSystemService::BUCKET_FTP);
			$remotePathOrderFile = "/{$context['workingFolder']}/$devFolderName/$customerId/$todayDate/OrderFiles";
			$this->fileBucketSrv->upload("$remotePathOrderFile/$levelStatusFilename", $localPath);

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
