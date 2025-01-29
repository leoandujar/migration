<?php

namespace App\Flow\Actions\Custom;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class KpFileFeedbackSendAction extends Action
{
	public const ACTION_DESCRIPTION = 'Send the feedback file';
	public const ACTION_INPUTS = [];
	public const ACTION_OUTPUTS = [
		'feedbackFilename' => [
			'description' => 'Name of the feedback file',
			'type' => 'string',
		],
	];
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
		$this->actionName = 'KpFileFeedbackSendAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filesStatusMapping = [];
		$context = $this->params;
		$criteria = $this->aux['criteria'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			foreach ($context['recipientMap'] as $filename => $value) {
				$feedBackStatus = 'Received';
				$feedBackReason = 'Good Record';
				if (!isset($context['filesTranslated'][$filename])) {
					$feedBackStatus = 'Pending';
					$feedBackReason = 'Invalid PDF';
				}
				$filesStatusMapping[$filename] = [
					'status' => $feedBackStatus,
					'reason' => $feedBackReason,
				];
			}

			$doc = new \DOMDocument();
			$doc->loadXML($context['xmlContent']);
			$nowDate = new \DateTime();
			$recipients = $doc->getElementsByTagName('recipient');

			foreach ($recipients as $recipient) {
				$fileName = $recipient->getElementsByTagName('fileName')->item(0)->nodeValue;
				if (!isset($filesStatusMapping[$fileName])) {
					continue;
				}
				$keyStatus = $doc->createElement('status', $filesStatusMapping[$fileName]['status']);
				$recipient->appendChild($keyStatus);

				$keyDate = $doc->createElement('date', $nowDate->format('m-d-Y'));
				$recipient->appendChild($keyDate);

				$keyTime = $doc->createElement('time', $nowDate->format('H:i:s'));
				$recipient->appendChild($keyTime);

				$keyReason = $doc->createElement('reason', $filesStatusMapping[$fileName]['reason']);
				$recipient->appendChild($keyReason);
			}

			$doc->formatOutput = true;
			$devFolderName = $context['testMode'] ? $context['testingFolder'] : $context['productionFolder'];
			$feedbackFileDate = (new \DateTime())->format('YmdHisv');
			$feedbackFilename = "PSPFeedback_{$context['orderNumber']}_$feedbackFileDate.xml";
			$remotePath = "{$context['destinationFolder']}/$devFolderName/{$context['fromFolder']}/ReturnFiles";
			$localFilePath = $context['translationsPath'] ?? $this->fileSystemSrv->filesPath;
			$localPath = "$localFilePath/$feedbackFilename";
			$this->fileSystemSrv->createOrOverrideFile($localPath, '');
			$doc->save($localPath);
			$this->fileBucketSrv->changeStorage(CloudFileSystemService::BUCKET_FTP);
			$this->fileBucketSrv->upload("$remotePath/$feedbackFilename", $localPath);
			unlink($localPath);

			$this->outputs = [
				'feedbackFilename' => $feedbackFilename,
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
