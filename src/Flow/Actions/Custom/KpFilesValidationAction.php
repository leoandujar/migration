<?php

namespace App\Flow\Actions\Custom;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class KpFilesValidationAction extends Action
{
	public const ACTION_DESCRIPTION = 'Validates the files in the translations folder with the metadata file.';
	public const ACTION_INPUTS = [
		'translationsPath' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'string',
			'description' => 'The path to the translations folder',
		],
	];
	public const ACTION_OUTPUTS = null;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->fileSystemSrv = $fileSystemSrv;
		$this->actionName = 'KpFilesValidationAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$translationsPath = $this->aux['translationsPath'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$metadataContent = file_get_contents("$translationsPath/metadata.txt");
			$lines = explode(PHP_EOL, $metadataContent);
			$metadata = [];
			foreach ($lines as $line) {
				if ('' === trim($line)) {
					continue;
				}
				list($key, $value) = explode('=', $line, 2);
				$metadata[trim($key)] = trim($value);
			}

			$filesToTranslate = $this->fileSystemSrv->getContentDir($translationsPath);
			if (count($filesToTranslate) - 1 !== intval($metadata['PDFCount'])) {
				$this->sendErrorMessage('[FLOW]: Error validating files with an xml maifest.', null, 'The number of files in the folder does not match the number of files in the manifest.', null);

				return self::ACTION_STATUS_ERROR;
			}

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage('[FLOW]: Error valigating files with an xml maifest.', null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
