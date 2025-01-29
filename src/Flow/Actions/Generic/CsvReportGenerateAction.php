<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class CsvReportGenerateAction extends Action
{
	private FileSystemService $fileSystemSrv;
	private CloudFileSystemService $fileBucketService;
	public const ACTION_DESCRIPTION = 'Generate a CSV report with the monitor results.';
	public const ACTION_INPUTS = [
		'headers' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'The headers of the CSV report. To be used as keys in the monitor result.',
		],
	];

	public const ACTION_OUTPUTS = [
		'csvReportUrl' => [
			'description' => 'URL to download the generated CSV report.',
			'type' => 'string',
		],
	];

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		FileSystemService $fileSystemSrv,
		CloudFileSystemService $fileBucketService,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->fileBucketService = $fileBucketService;
		$this->actionName = 'CsvReportGenerateAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$headers = $this->aux['headers'];
		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$errors = $this->monitorObj->getResult()['errors'];
			$success = $this->monitorObj->getResult()['successful'];
			$csvReportData = array_merge($errors, $success);

			$date = (new \DateTime())->format('Y-m-d H:i:s');
			$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, "report_{$date}");
			$csvPath = $this->fileSystemSrv->filesPath."/report_{$date}/{$this->monitorId}_report.csv";

			$csvFile = fopen($csvPath, 'w');
			fputcsv($csvFile, $headers);

			foreach ($csvReportData as $data) {
				$line = [];
				foreach ($headers as $header) {
					$line[] = $data[$header] ?? null;
				}
				fputcsv($csvFile, $line);
			}

			fclose($csvFile);

			$this->fileBucketService->changeStorage(CloudFileSystemService::BUCKET_WORKFLOW);
			$this->fileBucketService->upload("GeneralReports/{$this->monitorId}_report.csv", $csvPath);

			$temporaryUrl = $this->fileBucketService->getTemporaryUrl("GeneralReports/{$this->monitorId}_report.csv");

			$this->outputs = [
				'csvReportUrl' => $temporaryUrl,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;

		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::PROCESS_STATUS_FAILURE;
		}
	}
}
