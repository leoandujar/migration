<?php

namespace App\Flow\Actions\Generic;

use App\Connector\CustomerPortal\CustomerPortalConnector;
use App\Connector\Xtrf\Response\Projects\CreateProjectResponse;
use App\Connector\Xtrf\XtrfConnector;
use App\Flow\Actions\Action;
use App\Flow\Utils\FlowUtils;
use App\Model\Entity\ContactPerson;
use App\Model\Entity\WorkflowJobFile;
use App\Service\LoggerService;
use App\Service\Xtrf\XtrfProjectService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class XtrfProjectCreateAction extends Action
{
	public const ACTION_DESCRIPTION = 'Create a project or quote on XTRF';
	public const ACTION_INPUTS = [
		'simpleProject' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'bool',
			'description' => 'Boolean to indicate if the project is simple.',
		],
		'filesList' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
            'description' => 'Array with the files to upload to XTRF.',
		],
		'type' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				'project',
				'quote'],
            'description' => 'Type of the project or quote.',
		],
		'batch' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'integer',
            'description' => 'Batch number.',
		],
		'template' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
            'description' => 'Array with the template to create the project or quote.',
		],
		'nameOption' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
            'description' => 'Name option.',
		],
		'namePrefix' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
            'description' => 'Name prefix.',
		],
		'deadLine' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
            'description' => 'Deadline.',
		],
		'customFields' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'array',
            'description' => 'Array with the custom fields.',
		],
		'workingFilesAsRefFiles' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'bool',
            'description' => 'Boolean to indicate if the working files are reference files.',
		],
	];

	public const ACTION_OUTPUTS = [
		'projectsOrQuotes' => [
			'description' => 'List of created projects or quotes.',
			'type' => 'array',
		],
		'templatesLinked' => [
			'description' => 'List of templates linked to the created projects.',
			'type' => 'array',
		],
	];

	private const TYPE_PROJECT = 'project';
	private const TYPE_QUOTE = 'quote';
	private const MSG = 'Cannot create projects or quotes. Unable to continue';
	private XtrfProjectService $xtrfProjectSrv;
	private XtrfQuoteService $xtrfQuoteSrv;
	private XtrfConnector $xtrfConnector;
	private CustomerPortalConnector $customerPortalConnector;
	private FlowUtils $flowUtils;

	public function __construct(
		MonitorLogService $monitorLogSrv,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		XtrfProjectService $xtrfProjectSrv,
		XtrfQuoteService $xtrfQuoteSrv,
		XtrfConnector $xtrfConnector,
		CustomerPortalConnector $customerPortalConnector,
		FlowUtils $flowUtils,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->xtrfProjectSrv = $xtrfProjectSrv;
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->xtrfConnector = $xtrfConnector;
		$this->customerPortalConnector = $customerPortalConnector;
		$this->flowUtils = $flowUtils;
		$this->actionName = 'XtrfProjectCreateAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->sendStartMessage();

		$this->getInputs();

		$filesList = $this->aux['filesList'];
		$type = $this->aux['type'];
		$batch = $this->aux['batch'];
		$templates = $this->aux['template'];
		$name_option = $this->aux['nameOption'];
		$name_prefix = $this->aux['namePrefix'];
		$deadline = $this->aux['deadLine'];
		$custom_fields = $this->aux['customFields'];
		$workingFilesAsRefFiles = $this->aux['workingFilesAsRefFiles'];
		$simple_project = $this->aux['simpleProject'];

		$projectsOrQuotes = [];
		$templatesLinked = [];

		try {
			$this->setMonitorObject();

			if (array_keys($templates) !== range(0, count($templates) - 1)) {
				$templates = [$templates];
			}

			foreach ($templates as $template) {

				if (isset($template['projectRequests'])) {
					$templateTemp = $template['projectRequests'];
					$macroTemp = $template['macroParams'];

					$createResponse = $this->createXtrfProjectFunction($templateTemp);
					if (null === $createResponse) {
						continue;
					}

					$projectsOrQuotes[] = [
						'projectId' => $createResponse->getProject()->id,
						'macroParams' => $macroTemp,
					];

					continue;
				}

				$template = FlowUtils::orderTemplateTest($template, $deadline);

				if ($simple_project) {
					$createResponse = $this->createXtrfProjectFunction($template);
					if (null === $createResponse) {
						continue;
					}

					$projectsOrQuotes[] = $createResponse->getProject()->id;
					$templatesLinked[$createResponse->getProject()->id] = $template;
					continue;
				}

				$fullTemplate = $this->flowUtils->getFullTemplate($template, $type, $deadline, $name_option, $name_prefix);
				$template = $fullTemplate['template'];
				$specificData = $fullTemplate['specificData'];

				switch ($type) {
					case self::TYPE_PROJECT:
						$specificData['custom_fields'] = $custom_fields;
						$specificData['workingFilesAsRefFiles'] = $workingFilesAsRefFiles;
						foreach ($filesList as $file) {
							$fileData = [
								'name' => str_replace('/', '', $file['name']),
								'token' => $file['token'],
							];
							if (isset($file['ocr'])) {
								foreach ($file['ocr'] as $item) {
									$template['inputFiles'][] = $item;
								}
							}
							if (true === boolval($batch)) {
								try {
									unset($template['contactPerson'], $template['contact_person']);
									$template['inputFiles'][] = $fileData;
									$projectsOrQuotes[] = $this->standardCreateXtrfProjectsQuoteProcess($template, $specificData, $type);
									$template['inputFiles'] = [];
								} catch (\Throwable $thr) {
									$this->sendErrorMessage(
										self::MSG,
										[
											'reason' => 'project_create',
											'message' => self::MSG,
										],
										null,
										null
									);

									$template['inputFiles'] = [];
									continue;
								}
							} else {
								$template['inputFiles'][] = $fileData;
							}
						}
						break;

					case self::TYPE_QUOTE:
						$specificData['custom_fields'] = $custom_fields;
						if (!count($filesList)) {
							$this->loggerSrv->addWarning('[FLOW]: No files to process in Quote Workflow.');
						}
						foreach ($filesList as $file) {
							$template['inputFiles'][] = ['id' => $file['token']];
							if (isset($file['ocr'])) {
								foreach ($file['ocr'] as $item) {
									if (isset($item['name'])) {
										unset($item['name']);
									}
									$template['inputFiles'][] = $item;
								}
							}
							if (true === boolval($batch)) {
								try {
									$projectsOrQuotes[] = $this->standardCreateXtrfProjectsQuoteProcess($template, $specificData, $type);
									$template['inputFiles'] = [];
								} catch (\Throwable $thr) {
									$this->sendErrorMessage(
										self::MSG,
										[
											'reason' => 'project_create',
											'message' => self::MSG,
										],
										null,
										null
									);
									$template['inputFiles'] = [];
									continue;
								}
							}
						}
						break;
				}

				if (isset($template['inputFiles']) && count($template['inputFiles'])) {
					$projectsOrQuotes[] = $this->standardCreateXtrfProjectsQuoteProcess($template, $specificData, $type);
				}
			}
			$this->outputs = [
				'projectsOrQuotes' => $projectsOrQuotes,
				'templatesLinked' => $templatesLinked,
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

	private function standardCreateXtrfProjectsQuoteProcess($template, $specificData, $type)
	{
		switch ($type) {
			case self::TYPE_PROJECT:
				if (empty($specificData['contact_person'])) {
					$msg = 'Missing contact_person param for WF Project.';
					$this->sendErrorMessage(
						$msg,
						[
							'message' => $msg,
						],
						null,
						null
					);

					throw new BadRequestHttpException($msg);
				}

				$contactPersonId = $specificData['contact_person'];
				$contactPerson = $this->em->getRepository(ContactPerson::class)->find($contactPersonId);
				$template['customerId'] = $contactPerson?->getCustomersPerson()?->getCustomer()?->getId();

				$response = $this->xtrfConnector->createProject($template);

				if (!$response?->isSuccessfull() && null !== $response->getProject()) {
					$msg = 'Unable to create Project on XTRF for Project WF.';
					$this->sendErrorMessage(
						$msg,
						[
							'message' => $msg,
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						],
						null,
						null
					);

					throw new BadRequestHttpException($msg);
				}

				$responseAdditionalContact = $this->xtrfConnector->additionalContactPerson($response->getProject()->id, ['primaryId' => $contactPersonId]);
				if (!$responseAdditionalContact?->isSuccessfull()) {
					$this->loggerSrv->addWarning("Unable to link additional contact person to project {$response->getProject()->id}");
				}

				$this->xtrfConnector->updateProjectCustomFields($response->getProject()->id, $specificData['custom_fields']);

				if ($specificData['workingFilesAsRefFiles'] && !empty($template['inputFiles'])) {
					foreach ($template['inputFiles'] as $ref) {
						$token = is_array($ref) && isset($ref['token']) ? $ref['token'] : $ref;
						$refData[] = [
							'token' => $token,
							'category' => WorkflowJobFile::CATEGORY_REF,
						];
					}

					if (count($refData)) {
						$projectGetResponse = $this->xtrfConnector->getProject($response->getProject()->id);
						if ($projectGetResponse->isSuccessfull()) {
							$this->xtrfProjectSrv->updateTaskFiles($refData, $projectGetResponse->getProject()?->tasks);
						}
					}
				}

				$this->sendSuccess(
					[
						'id' => $response->getProject()->id,
						'number' => $response->getProject()->idNumber,
						'name' => $response->getProject()->name,
					]
				);

				$this->loggerSrv->addInfo("[GEN-WF]: Project created! {$response->getProject()->id}");

				return $response->getProject();
			case self::TYPE_QUOTE:
				if ($specificData['workingFilesAsRefFiles']) {
					$template['referenceFiles'] = $template['inputFiles'];
				}
				$dataParams = $this->xtrfQuoteSrv->prepareCreateData($template);
				$response = $this->customerPortalConnector->createQuote($dataParams, $specificData['sessionID']);

				if (!$response->isSuccessfull()) {
					$msg = 'Unable to create Quote on XTRF for Quote WF.';
					$this->sendErrorMessage(
						$msg,
						[
							'message' => $msg,
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						],
						null,
						null
					);

					throw new BadRequestHttpException($msg);
				}

				$quote = $response->getQuoteDto();

				if (null !== $quote) {
					if ($specificData['instructions']) {
						$this->xtrfConnector->updateQuoteInstructions($quote->id, $specificData['instructions']);
					}

					$this->sendSuccess(
						[
							'id' => $quote->id,
							'number' => $quote->idNumber,
							'name' => $quote->name,
						]
					);

					$this->loggerSrv->addInfo("[FLOW]: Quote created! {$quote->id}");

					return $quote;
				}
				break;
		}

		return [];
	}

	private function createXtrfProjectFunction(array $template): ?CreateProjectResponse
	{
		$createResponse = $this->xtrfConnector->createProject($template);
		if (!$createResponse->isSuccessfull()) {
			$msg = 'Project could not be created on XTRF. Unable to continue.';
			$this->sendErrorMessage($msg, [
				'message' => $msg,
			], null, null);

			return null;
		}

		return $createResponse;
	}
}
