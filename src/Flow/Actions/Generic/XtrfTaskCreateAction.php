<?php

namespace App\Flow\Actions\Generic;

use App\Connector\Xtrf\XtrfConnector;
use App\Flow\Actions\Action;
use App\Model\Entity\WorkflowJobFile;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

class XtrfTaskCreateAction extends Action
{
	public const ACTION_DESCRIPTION = 'Create a task on XTRF';
	public const ACTION_INPUTS = [
		'templatesLinked' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the templates linked to the projects or quotes.',
		],
		'projectsOrQuotes' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'Array with the projects or quotes to create task.',
		],
		'filesTaskMapping' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Array with the files to be translated (with -token- key in array).',
		],
		'taskCreationMapping' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Array with the mapping of target languages and workflows.',
		],
		'workingFilesAsRefFiles' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'bool',
			'description' => 'Boolean to indicate if the working files should be uploaded as reference files.',
		],
	];

	public const ACTION_OUTPUTS = [
		'totalFiles' => [
			'description' => 'Total of files to be translated.',
			'type' => 'integer',
		],
		'filesTranslated' => [
			'description' => 'List of files that were translated.',
			'type' => 'array',
		],
	];
	private XtrfConnector $xtrfConnector;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		XtrfConnector $xtrfConnector,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->xtrfConnector = $xtrfConnector;
		$this->actionName = 'XtrfTaskCreateAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->sendStartMessage();

		$this->getInputs();

		$projectsOrQuotes = $this->aux['projectsOrQuotes'];
		$filesTaskMapping = $this->aux['filesTaskMapping'];
		$taskCreationMapping = $this->aux['taskCreationMapping'];
		$templatesLinked = $this->aux['templatesLinked'];
		$workingFilesAsRefFiles = $this->aux['workingFilesAsRefFiles'];

		try {
			$this->setMonitorObject();

			$totalFiles = 0;
			$filesToken = [];
			$filesTranslated = [];

			foreach ($projectsOrQuotes as $poq) {
				$template = $templatesLinked[$poq];
				foreach ($filesTaskMapping as $fileData) {
					$token = $fileData['token'];
					$filesToken[$fileData['target_language']][$fileData['workflow_id']] = $token;
					$filesTranslated[$fileData['filename']] = true;
					++$totalFiles;
				}

				foreach ($taskCreationMapping as $targetLanguageId => $workflows) {
					foreach ($workflows as $workflowId) {
						$dataCreate = [
							'specializationId' => $template['specializationId'],
							'workflowId' => $workflowId,
							'languageCombination' => [
								'sourceLanguageId' => $template['sourceLanguageId'],
								'targetLanguageId' => $targetLanguageId,
							],
						];
						$createTaskResponse = $this->xtrfConnector->createAdditionalTaskRequest($poq, $dataCreate);

						if (!$createTaskResponse->isSuccessfull()) {
							$this->loggerSrv->addError("Unable to create task for language $targetLanguageId. Skipping");
						}
						$filesPerWorkflow = $filesTokens[$targetLanguageId][$workflowId] ?? [];

						foreach ($filesPerWorkflow as $token) {
							$taskId = $createTaskResponse->getRaw()['id'];
							$data = [
								'token' => $token,
								'category' => WorkflowJobFile::CATEGORY_WORKFILE,
							];

							$workingUploadResponse = $this->xtrfConnector->uploadTaskFile(strval($taskId), $data);
							if (!$workingUploadResponse->isSuccessfull()) {
								$this->loggerSrv->addError("Unable to upload working file to task $taskId. Skipping");
							}

							if ($workingFilesAsRefFiles) {
								$data = [
									'token' => $token,
									'category' => WorkflowJobFile::CATEGORY_REF,
								];

								$refUploadResponse = $this->xtrfConnector->uploadTaskFile(strval($taskId), $data);
								if (!$refUploadResponse->isSuccessfull()) {
									$this->loggerSrv->addError("Unable to upload reference file to task $taskId. Skipping");
								}
							}
						}
					}
				}

				$projectResponse = $this->xtrfConnector->getProject($poq);
				if (!$projectResponse->isSuccessfull()) {
					$msg = "Unable to fetch project previously created: {$poq} from XTRF. Unable to continue.";
					$this->sendErrorMessage($msg, ['message' => $msg], null, null);
				}
			}

			$this->outputs = [
				'totalFiles' => $totalFiles,
				'filesTranslated' => $filesTranslated,
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
