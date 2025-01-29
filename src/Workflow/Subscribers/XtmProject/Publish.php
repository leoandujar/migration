<?php

namespace App\Workflow\Subscribers\XtmProject;

use App\Command\Services\Helper;
use App\Service\UtilService;
use App\Service\LoggerService;
use App\Connector\Xtm\XtmConnector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use App\Model\Repository\WFHistoryRepository;
use App\Service\Notification\NotificationService;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Publish implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private XtmConnector $xtmConnector;
	private KernelInterface $kernel;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationService;
	private EntityManagerInterface $em;
	private UtilService $utilService;
	private WFHistoryRepository $historyRepository;

	/**
	 * Publish constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		WFHistoryRepository $historyRepository,
		XtmConnector $xtmConnector,
		KernelInterface $kernel,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationService,
		EntityManagerInterface $em,
		UtilService $utilService
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry = $registry;
		$this->historyRepository = $historyRepository;
		$this->xtmConnector = $xtmConnector;
		$this->kernel = $kernel;
		$this->parameterBag = $parameterBag;
		$this->notificationService = $notificationService;
		$this->em = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_PROJECT);
		$this->utilService = $utilService;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_project.completed.prepared' => 'publish',
		];
	}

	public function publish(Event $event)
	{
		try {
			$history = $event->getSubject();
			$context = $history->getContext();
			$wf = $this->registry->get($history, 'xtm_project');
			$testFlag = '';
			$now = (new \DateTime());
			$deadLine = $context['dueDate'];
			if (is_numeric($deadLine)) {
				$deadLine = sprintf('%dD', $deadLine);
			}
			$deadLine = Helper::deadLine($deadLine);
			$name = sprintf('%s-%s-%s', $context['namePrefix'], $context['project_params'][0]['name'] ?? $this->utilService->generateRandomString(), $now->format('Y-m-d'));
			$templateID = $context['template_html'];
			$description = sprintf('%s - %s', $name, $now->format('Y-m-d'));

			if (array_key_exists('xtrfProject', $context)) {
				$name = sprintf('%s_%s', $context['xtrfProject'], $context['project_params'][0]['name'] ?? $this->utilService->generateRandomString());
			}

			if (array_key_exists('priority', $context)) {
				$name = sprintf('%s_PRIORITY', $name);
			}

			$firsFileName = $context['project_params'][0]['name'] ?? $this->utilService->generateRandomString();
			$fileName = explode('.', $firsFileName);

			if (isset($fileName[1]) && 'json' === $fileName[1]) {
				$templateID = $context['template_json'];
			}

			if (array_key_exists('test', $context)) {
				$name = sprintf('%s_TEST', $name);
				$templateID = $context['template_test'];
				$testFlag = '&test=1';
			}

			$projectParams = [
				'customerId' => $context['customerId'],
				'name' => $name,
				'description' => $description,
				'dueDate' => $deadLine->format('Y-m-d'),
				'templateId' => $templateID,
			];

			if (isset($context['project_params'])) {
				foreach ($context['project_params'] as $key => $project_param) {
					$tmpFile = sprintf('%s/var/%s', $this->kernel->getProjectDir(), md5(random_bytes(10)));
					$file = fopen($tmpFile, 'a+');
					fwrite($file, base64_decode($project_param['content']));
					fclose($file);
					$projectParams['translationFiles['.$key.'].name'] = $project_param['name'];
					$projectParams['translationFiles['.$key.'].file'] = fopen($tmpFile, 'r');
				}
			}
			$deliveryWorkflow = sprintf('&workflow=%s', $context['deliveryWorkflow']);
			$projectParams['callbacks.projectFinishedCallback'] =
				sprintf(
					'%s?internalCustomerID=%s%s%s',
					$context['callbackURL'],
					$context['customerId'],
					$deliveryWorkflow,
					$testFlag
				);
			$rsp = $this->xtmConnector->createProject($projectParams);
			if (null !== $rsp) {
				if (isset($rsp->getRaw()['projectId'])) {
					$context['project'] = [
						'status' => $rsp->isSuccessfull(),
						'name' => $name,
						'projectId' => $rsp->getRaw()['projectId'],
					];
				}
			}
			if ($wf->can($history, 'finished')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				switch ($context['notification_type']) {
					case NotificationService::NOTIFICATION_TYPE_TEAM:
						$data = [
							'message' => sprintf('Project: %s created successfully in XTM', $name),
							'status' => $context['project']['status'],
							'date' => (new \DateTime())->format('Y-m-d'),
							'title' => $history->getName(),
						];
						break;
					case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
						$template = $this->parameterBag->get('app.postmark.tpl_id.workflow');
						if (!empty($template)) {
							$data = [
								'template' => $template,
								'workflow' => sprintf('%s- Project Name: %s With ID: %s Status: %s-', $wf->getName(), $context['project']['name'], $context['project']['projectId'], $context['project']['status']),
								'link' => '#',
							];
						}
						break;
				}
				$this->notificationService->addNotification($context['notification_type'], $context['notification_target'], $data, $wf->getName());

				$wf->apply($history, 'finished');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
