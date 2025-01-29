<?php

namespace App\Service;

use App\Linker\Services\RedisClients;
use App\Model\Entity\AVCustomerRule;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\WFWorkflow;
use App\Service\Notification\NotificationService;
use Doctrine\ORM\EntityManagerInterface;

class XtrfWebhookService
{
	public const string TYPE_WORKFLOW = 'workflow';
	public const string TYPE_NOTIFICATION = 'notification';

	public const string EVENT_TASKS_FILES_READY = 'task_files_ready';
	public const string EVENT_PROJECT_CREATED = 'project_created';
	public const string EVENT_PROJECT_STATUS_CHANGED = 'project_status_changed';
	public const string EVENT_QUOTE_CREATED = 'quote_created';
	public const string EVENT_QUOTE_STATUS_CHANGED = 'quote_status_changed';
	public const string EVENT_JOB_STATUS_CHANGED = 'job_status_changed';
	public const string EVENT_CUSTOMER_CREATED = 'customer_created';
	public const string EVENT_CUSTOMER_UPDATED = 'customer_updated';

	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private RedisClients $redisClients;
	private NotificationService $notificationSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		RedisClients $redisClients,
		NotificationService $notificationSrv
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->notificationSrv = $notificationSrv;
	}

	public function processEvents(string $event, $objectData): void
	{
		$ruleList = $this->em->getRepository(AVCustomerRule::class)->findBy([
			'event' => $event,
		]);

		foreach ($ruleList as $rule) {
			$customer = $rule->getCustomer();
			$filters = $rule->getFilters();

			if (!empty([$filters['status']]) && strtolower($customer->getStatus()) !== strtolower($filters['status'])) {
				break;
			}

			// TO-DO ADD MISSING FILTERS USING $objectData

			switch ($rule->getType()) {
				case self::TYPE_NOTIFICATION:
					$this->processNotification($rule->getParameters());
					break;
				case self::TYPE_WORKFLOW:
					$this->processWorkflow($rule->getWorkflow(), $rule->getParameters());
					break;
			}
		}
	}

	private function processNotification(array $data): void
	{
		$type = $data['notification_type'] ?? null;
		if (empty($type)) {
			$this->loggerSrv->addWarning('Rule with type Notification type has not notification_type into parameters.');

			return;
		}
		switch ($type) {
			case NotificationService::NOTIFICATION_TYPE_TEAM:
				$target = $data['target'] ?? null;
				$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, $target, $data);
				break;
			case NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL:
				if (empty($data['email'])) {
					$this->loggerSrv->addWarning('Rule with type Notification-Email has not email into parameters.');
					break;
				}
				if (empty($data['subject'])) {
					$this->loggerSrv->addWarning('Rule with type Notification-Email has not subject into parameters.');
					break;
				}

				// CAN BE CLASSIC NOTIFICATION OR POSTMARK EMAIL.
				$this->notificationSrv->addNotification(
					NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
					$data['email'],
					[
						'subject' => $data['subject'],
						'template' => $data['template'],
						'data' => $data['data'],
					]
				);
				break;
		}
	}

	private function processWorkflow(?WFWorkflow $workflow, ?array $params): void
	{
		if (!$workflow) {
			$this->loggerSrv->addWarning('Rule with type Workflow type has workflow related.');
			return;
		}

		$workflowMonitor = new AVWorkflowMonitor();
		$workflowMonitor->setWorkflow($workflow);
		if ($params) {
			$currentParams = $workflow->getParameters();
			$workflowMonitor->setDetails(['params' => array_merge($currentParams->getParams(), $params)]);
		}
		$this->em->persist($workflowMonitor);
		$this->em->flush();
		$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_AWAITING_WORKFLOWS, json_encode(['id' => $workflowMonitor->getId()]));
	}
}
