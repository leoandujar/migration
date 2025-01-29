<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\FileSystem\FileSystemService;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Twig\Environment;

class PdfGeneratorAction extends Action
{
	public const ACTION_DESCRIPTION = 'Generate PDFs for the documents';
	public const ACTION_INPUTS = [
		'pdfTemplate' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'The template for the PDF generation.',
		],
		'startDate' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The start date of the documents.',
		],
		'documentsResult' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'The documents to be used for the PDF generation.',
			'canReplacedFor' => 'files',
		],
	];

	public const ACTION_OUTPUTS = [
		'pdfPaths' => [
			'description' => 'List of paths of the generated PDFs.',
			'type' => 'array',
		],
	];
	private Environment $env;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		Environment $env,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->env = $env;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->actionName = 'PdfGeneratorAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$template = $this->aux['pdfTemplate'];
		$startDate = $this->aux['startDate'];
		$documentsResult = $this->aux['documentsResult'];

		$this->sendStartMessage();

		try {
			$pdfPaths = [];

			$this->setMonitorObject();

			$folderName = uniqid('attestation_output_');
			$this->fileSystemSrv->createTempDir($folderName);
			$folderName = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$folderName;

			foreach ($documentsResult as $filename => $info) {
				$document = $info['file'];
				$language = $info['language'];
				$project = $info['project'];
				$mpdf = new Mpdf();
				$mpdf->setAutoBottomMargin = 'stretch';
				$mpdf->setAutoTopMargin = 'stretch';
				$this->fileSystemSrv->createDirectory($folderName, $project);

				$currentDir = realpath(__DIR__);
				$baseDir = dirname($currentDir, 4);
				$templatePath = $baseDir.'/templates/Emails/attestation_tpl.html.twig';
				$tpl = $this->env->createTemplate(file_get_contents($templatePath));
				$content = $tpl->render([
					'contact' => [
						'name' => $template['name'],
						'address' => $template['address'],
						'email' => $template['email'],
					],
					'docData' => [
						'member' => $document['member'] ?? '',
						'type' => $document['type'] ?? '',
						'language' => $language ?? '',
						'date' => $startDate ?? '',
					],
				]);
				$mpdf->WriteHTML($content);
				$pdfPath = "$folderName/$project/attestation_$filename.pdf";

				try {
					$mpdf->Output($pdfPath, Destination::FILE);
					$this->loggerSrv->addInfo("[FLOW]: PDF generated for $filename");
					$pdfPaths[] = $pdfPath;
				} catch (\Throwable $thr) {
					$this->loggerSrv->addWarning("[FLOW]: Error generating PDF for $filename");
					continue;
				}
			}

			$this->outputs = [
				'pdfPaths' => $pdfPaths,
			];

			$this->setOutputs();

			$this->sendSuccessMessage();

			$this->outputs = [];

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}
}
