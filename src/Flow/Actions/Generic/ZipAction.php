<?php

/*
 *  - ZipAction -
 *
 *  This action will create a zip file with the given pdf paths. At the
 *  moment they are only considered "filesPath". By the more progress in
 *  the workflow migration more things will be supported.
 *
 *  -> Inputs:
 *    - pdfPaths: array with the paths of the pdf files to be zipped.
 *
 * -> Outputs:
 *   - zipPath: string with the path of the zip file created (For Notify).
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class ZipAction extends Action
{
	private const ATTESTATIONS = 'Attestations';
	private const TRANSLATIONS = 'Translations';

	public const ACTION_DESCRIPTION = 'Zip files';
	public const ACTION_INPUTS = [
		'pdfPaths' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the paths of the pdf files to be zipped.',
			'canReplacedFor' => 'files',
		],
		'zipType' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'select',
			'select-values' => [
				self::ATTESTATIONS,
			],
			'description' => 'Type of zip to create.',
		],
		'filesUnZipped' => [
			'required' => false,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the paths of the files to be zipped.',
			'canReplacedFor' => 'bool',
		],
		'orderNumber' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'details' => 'NO USED',
		],
		'valueNumber' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'details' => 'NO USED',
		],
	];

	public const ACTION_OUTPUTS = [
		'zipPath' => [
			'description' => 'Path of the zip file created.',
			'type' => 'string',
		],
		'globalDate' => [
			'description' => 'Global date for the zip file.',
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
		$this->actionName = 'ZipAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$pdfPaths = $this->aux['pdfPaths'] ?? null;
		$filesUnZipped = $this->aux['filesUnZipped'] ?? null;
		$ziptype = $this->aux['zipType'];
		$ziptype = match ($ziptype) {
			self::ATTESTATIONS => 1,
			self::TRANSLATIONS => 2,
			default => 1,
		};
		$orderNumber = $this->aux['orderNumber'] ?? 'Order number unknown';
		$valueNumber = $this->aux['valueNumber'] ?? 'Value number unknown';

		$this->sendStartMessage();

		$files = (null !== $pdfPaths) ? $pdfPaths : $filesUnZipped;

		try {
			$this->setMonitorObject();

			$zipper = new \ZipArchive();
			$now = new \DateTime();
			$zipName = $this->resolveType($ziptype, $orderNumber, $valueNumber, $now);
			$zipPath = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$zipName;
			$zipper->open($zipPath, \ZipArchive::CREATE);

			foreach ($files as $file) {
				$path = is_array($file) && (array_keys($file) !== range(0, count($file) - 1)) ? $file['pathLocalFS'] : $file;
				$filename = pathinfo($path, PATHINFO_BASENAME);

				try {
					$zipper->addFile($path, $filename);
				} catch (\Throwable $e) {
					$this->loggerSrv->addWarning("[FLOW]: Error adding $filename to the zip: {$e->getMessage()}");
					$this->loggerSrv->addInfo("[FLOW]: Error adding $filename ZipAction.", $e->getMessage());
					continue;
				}
			}

			$zipper->close();

			$this->sendSuccessMessage();

			$this->outputs = [
				'zipPath' => $zipPath,
				'globalDate' => $now->format('YmdHisv'),
			];

			$this->setOutputs();

			$this->outputs = [];

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}

	private function resolveType(int $type, string $orderNumber, string $valueNumber, \DateTime $now): string
	{
		return match ($type) {
			self::ATTESTATIONS => "attestations_{$now->format('Y_m_d_H_i_s')}.zip",
			self::TRANSLATIONS => "RETURN_TRANSLATION_{$orderNumber}_{$valueNumber}_{$now->format('YmdHisv')}_order_PSP.zip",
			default => 'attestations',
		};
	}
}
