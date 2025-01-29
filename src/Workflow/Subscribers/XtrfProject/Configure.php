<?php

namespace App\Workflow\Subscribers\XtrfProject;

use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use App\Service\Xtrf\XtrfQuoteService;
use App\Model\Entity\WorkflowJobFile;
use App\Model\Entity\AVWorkflowMonitor;
use App\Connector\Xtrf\XtrfConnector;
use App\Service\Xtrf\XtrfProjectService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Workflow\Services\XtrfProject\Start;
use App\Workflow\HelperServices\MonitorLogService;
use App\Model\Repository\ContactPersonRepository;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Configure implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private XtrfConnector $xtrfConnector;
	private CustomerPortalConnector $customerPortalConnector;
	private EntityManagerInterface $em;
	private XtrfQuoteService $xtrfQuoteSrv;
	private MonitorLogService $monitorLogSrv;
	private WorkflowMonitorRepository $wfMonitorRepo;
	private ContactPersonRepository $contactPersonRepo;
	private XtrfProjectService $xtrfProjectSrv;

	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		XtrfConnector $xtrfConnector,
		XtrfQuoteService $xtrfQuoteSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		WorkflowMonitorRepository $wfMonitorRepo,
		XtrfProjectService $xtrfProjectSrv,
		ContactPersonRepository $contactPersonRepo,
		CustomerPortalConnector $customerPortalConnector
	) {
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->registry = $registry;
		$this->xtrfConnector = $xtrfConnector;
		$this->customerPortalConnector = $customerPortalConnector;
		$this->em = $em;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
		$this->contactPersonRepo = $contactPersonRepo;
		$this->xtrfProjectSrv = $xtrfProjectSrv;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtrf_project.completed.published' => 'configure',
		];
	}

	/**
	 * @throws \Throwable
	 */
	public function configure(Event $event)
	{
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$wf = $this->registry->get($history, 'xtrf_project');
		$context = $history->getContext();
		$context['status'] = 'failed';
		if ($context['monitor_id']) {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
		}
		try {
			switch ($context['type']) {
				case Start::TYPE_PROJECT:
					foreach ($context['files'] as $file) {
						$fileData = [
							'name' => str_replace('/', '', $file['filename']),
							'token' => $file['token'],
						];
						if (isset($file['ocr_file'])) {
							foreach ($file['ocr_file'] as $item) {
								$context['template']['inputFiles'][] = $item;
							}
						}
						if (true === boolval($context['batch'])) {
							try {
								$context['template']['inputFiles'][] = $fileData;
								$this->processCreateProjectorQuote($context);
								$context['template']['inputFiles'] = [];
							} catch (\Throwable $thr) {
								$msg = 'Cannot create projects. Unable to continue';
								$this->monitorLogSrv->appendError([
									'message' => $msg,
								]);
								$this->loggerSrv->addError("XTRF Project failed {$file['filename']}", $thr);
								$context['template']['inputFiles'] = [];
								continue;
							}
						} else {
							$context['template']['inputFiles'][] = $fileData;
						}
					}
					break;
				case Start::TYPE_QUOTE:
					if (!count($context['files'])) {
						$this->loggerSrv->addWarning('No files to process in Quote Workflow.');
					}
					foreach ($context['files'] as $file) {
						$context['template']['inputFiles'][] = ['id' => $file['token']];
						if (isset($file['ocr_file'])) {
							foreach ($file['ocr_file'] as $item) {
								if (isset($item['name'])) {
									unset($item['name']);
								}
								$context['template']['inputFiles'][] = $item;
							}
						}
						if (true === boolval($context['batch'])) {
							try {
								$this->processCreateProjectorQuote($context);
								$context['template']['inputFiles'] = [];
							} catch (\Throwable $thr) {
								$msg = 'Cannot create quotes. Unable to continue';
								$this->monitorLogSrv->appendError([
									'message' => $msg,
								]);
								$this->loggerSrv->addError("XTRF Quote failed {$file['filename']}", $thr);
								$context['template']['inputFiles'] = [];
								continue;
							}
						}
					}
					break;
			}
			if (isset($context['template']['inputFiles']) && count($context['template']['inputFiles'])) {
				$this->processCreateProjectorQuote($context);
			}
			if ($history instanceof WFHistory) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
			}
			if ($wf->can($history, 'configured')) {
				$wf->apply($history, 'configured');
			}
		} catch (\Throwable $thr) {
			$this->monitorLogSrv->appendError([
				'message' => 'Workflow finished with error',
			]);
			$this->loggerSrv->addError('Workflow finished with error', $thr);
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}

	/**
	 * @throws \Exception
	 */
	private function processCreateProjectorQuote(&$context)
	{
		if (is_array($context['files']) && count($context['files'])) {
			switch ($context['type']) {
				case Start::TYPE_PROJECT:
					if (empty($context['contact_person'])) {
						$msg = 'Missing contact_person param for WF Project.';
						$this->loggerSrv->addError($msg);
						$this->monitorLogSrv->appendError([
							'message' => $msg,
						]);
						throw new BadRequestHttpException($msg);
					}

					$contactPersonId = $context['contact_person'];
					$contactPerson = $this->contactPersonRepo->find($contactPersonId);
					$context['template']['customerId'] = $contactPerson?->getCustomersPerson()?->getCustomer()?->getId();

					$response = $this->xtrfConnector->createProject($context['template']);
					if (!$response?->isSuccessfull() && null !== $response->getProject()) {
						$msg = 'Unable to create Project on XTRF for Project WF.';
						$this->loggerSrv->addError($msg, [
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						]);
						$this->monitorLogSrv->appendError([
							'message' => $msg,
						]);
						throw new BadRequestHttpException($msg);
					}

					$responseAdditionalContact = $this->xtrfConnector->additionalContactPerson($response->getProject()->id, ['primaryId' => $contactPersonId]);
					if (!$responseAdditionalContact?->isSuccessfull()) {
						$this->loggerSrv->addWarning("Unable to link additional contact person to project {$response->getProject()->id}");
					}
					$context['request']['data'][] = [
						'name' => $response->getProject()->name,
						'info' => $response->getProject(),
					];
					$context['request']['links'][] = $context['download_link_url'].$response->getProject()->id;
					$this->xtrfConnector->updateProjectCustomFields($response->getProject()->id, $context['custom_fields']);
					if ($context['ref_from_input_files'] && !empty($context['template']['inputFiles'])) {
						foreach ($context['template']['inputFiles'] as $ref) {
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

					$this->monitorLogSrv->appendSuccess([
						'id' => $response->getProject()->id,
						'number' => $response->getProject()->idNumber,
						'name' => $response->getProject()->name,
					]);
					$context['info'] = sprintf('New XTRF project %s', $response->getProject()->idNumber);
					$context['status'] = 'success';
					break;
				case Start::TYPE_QUOTE:
					$context['info'] = 'Unable to create new XTRF Quote';
					$context['status'] = 'failed';
					if ($context['ref_from_input_files']) {
						$context['template']['referenceFiles'] = $context['template']['inputFiles'];
					}
					$dataParams = $this->xtrfQuoteSrv->prepareCreateData($context['template']);
					$response = $this->customerPortalConnector->createQuote($dataParams, $context['sessionID']);
					if (!$response->isSuccessfull()) {
						$msg = 'Unable to create Quote on XTRF for Quote WF.';
						$this->loggerSrv->addError($msg, [
							'raw' => print_r($response->getRaw(), true),
							'detailedMessage' => $response->getDetailedMessage(),
							'errorMessage' => $response->getErrorMessage(),
						]);
						$this->monitorLogSrv->appendError([
							'message' => $msg,
						]);
						throw new BadRequestHttpException($msg);
					}
					$quote = $response->getQuoteDto();
					if (null !== $quote) {
						if ($context['instructions']) {
							$this->xtrfConnector->updateQuoteInstructions($quote->id, $context['instructions']);
						}
						$context['request']['data'][] = [
							'name' => $quote->name,
							'info' => $quote,
						];
						$this->monitorLogSrv->appendSuccess([
							'id' => $quote->id,
							'number' => $quote->idNumber,
							'name' => $quote->name,
						]);
						$context['request']['links'][] = $context['download_link_url'].$quote->id;
						$context['info'] = sprintf('New XTRF quote %s', $quote->idNumber);
						$context['status'] = 'success';
					}
					break;
			}
		}
	}
}
