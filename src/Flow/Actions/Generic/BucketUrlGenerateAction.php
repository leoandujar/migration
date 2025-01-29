<?php

/*
 *  - BucketUrlGenerateAction
 *
 *  Based on the path of each file you will obtain its
 *  temporaryUrl. Ideal for files close to the Azure OCR process
 *  or if you are looking to work with the temporaryUrl.
 *
 *  -> Inputs:
 *     - sourceDisk: The source disk of the files. From they will bring.
 *     - filesList: The list of files to get the temporaryUrl.
 *
 *  -> Outputs:
 *    - filesList: The list of files with the temporaryUrl (key).
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BucketUrlGenerateAction extends Action
{
	public const ACTION_DESCRIPTION = 'Generate temporary URL for files in a bucket';
	public const ACTION_INPUTS = [
		'sourceDisk' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'integer',
			'description' => 'Bucket to get files temporary URL.',
		],
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of files to get the temporary URL.',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = [
		'filesList' => [
			'description' => 'Add temporaryUrl key to filesList.',
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
		$this->actionName = 'BucketUrlGenerateAction';
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

			$this->fileBucketService->changeStorage($sourceDisk);

			foreach ($filesList as $index => $file) {
				$result = $this->getTemporaryUrl($file);
				if (null === $result) {
					throw new BadRequestHttpException('[FLOW]: Error getting temporary URL. For file: '.$file['name']);
				}
				$filesList[$index]['temporaryUrl'] = $result;
				if (isset($file['ocr'])) {
					foreach ($file['ocr'] as $key => $ocr) {
						$result = $this->getTemporaryUrl($ocr);
						if (null === $result) {
							throw new BadRequestHttpException('[FLOW]: Error getting temporary URL. For file: '.$file['name'].'In OCR data');
						}
						$filesList[$index]['ocr'][$key]['temporaryUrl'] = $result;
					}
				}
			}

			$this->outputs = [
				'filesList' => $filesList,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage('Failed getting temporary URL', null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}

	private function getTemporaryUrl($file): ?string
	{
		if (isset($file['temporaryUrl']) && null !== $file['temporaryUrl']) {
			return $file['temporaryUrl'];
		} else {
			$temporaryUrl = $this->fileBucketService->getTemporaryUrl($file['path']);
			if (empty($temporaryUrl)) {
				$this->sendErrorMessage(
					'Failed getting temporary URL. For file: '.$file['path'],
					[
						'reason' => 'temporary_url_error',
						'file' => $file['name'],
					],
					null,
					null
				);
				$this->loggerSrv->addWarning('[FLOW]: Error getting TemporaryUrl');

				return null;
			}

			return $temporaryUrl;
		}
	}
}
