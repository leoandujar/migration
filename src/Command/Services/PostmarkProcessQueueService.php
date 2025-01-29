<?php

namespace App\Command\Services;

use App\Workflow\Services\WorkflowInterface;
use App\Workflow\Services;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFWorkflow;
use App\Linker\Services\RedisClients;
use App\Model\Repository\WorkflowRepository;
use App\Service\Notification\NotificationService;
use App\Model\Repository\InternalUserRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Workflow\WorkflowServiceFactory;

class PostmarkProcessQueueService
{
	public const TYPE_POSTMARK_EMAIL_WEBHOOK = 1;
	public const TYPE_POSTMARK_EMAIL_SMS = 2;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private WorkflowRepository $wfRepository;
	private NotificationService $notificationSrv;
	private InternalUserRepository $iuRepository;
	private WorkflowServiceFactory $workflowSrvFactory;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		InternalUserRepository $iuRepository,
		NotificationService $notificationSrv,
		WorkflowRepository $wfRepository,
		WorkflowServiceFactory $workflowSrvFactory
	) {
		$this->redisClients = $redisClients;
		$this->loggerSrv = $loggerSrv;
		$this->wfRepository = $wfRepository;
		$this->notificationSrv = $notificationSrv;
		$this->iuRepository = $iuRepository;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->workflowSrvFactory = $workflowSrvFactory;
	}

	public function dequeueAndProcess(OutputInterface $output, int $dequeueLimit = 10000): void
	{
		do {
			$totalProcessed = 0;
			$output->writeln('PROCESSING POSTMARK QUEUE.');
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_POSTMARK_WEBHOOK_QUEUE)) !== null) {
				if (null === $payload) {
					$msg = 'Postmark queue is empty.';
					$this->loggerSrv->addWarning($msg);
					$output->writeln($msg);
					$dequeueLimit = 0;
				}
				try {
					if (($postmarkObj = unserialize($payload)) === false || !is_object($postmarkObj)) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}

					$processedResponse = match ($postmarkObj->type) {
						self::TYPE_POSTMARK_EMAIL_WEBHOOK => $this->processWebhook($postmarkObj),
						self::TYPE_POSTMARK_EMAIL_SMS => $this->processSms($postmarkObj),
					};

					++$totalProcessed;
					if (true !== $processedResponse) {
						--$totalProcessed;
						$this->enqueueDueError($postmarkObj, $output);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Postmark object from queue. Check logs for more details.', $thr);
					$this->enqueueDueError($postmarkObj, $output);
					--$totalProcessed;
					continue;
				}
			}
		} while (0);
		$output->writeln(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
	}

	private function processSms(mixed $object): bool
	{
		$smsText = $object->smsText;
		$tag = $object->workflowName;
		if (empty($smsText) || empty($tag)) {
			$this->loggerSrv->addError('Tag or SmsText empty for postmark SMS process');

			return false;
		}
		$mobiles = $this->iuRepository->findByTag($tag);

		foreach ($mobiles as $data) {
			try {
				$mobile = $data['mobile'];
				$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_SMS, $mobile, [
					'smsText' => $smsText,
				], 'Postmark SMS');
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Unable to enqueue SMS notification from postmark SMS, mobile=>$mobile", $thr);
				continue;
			}
		}

		return true;
	}

	/**
	 * @throws \Throwable
	 */
	private function processWebhook(mixed $object): bool
	{
		$wfName = $object->workflowName;

		if (empty($wfName)) {
			$msg = 'There is not Workflow Name defined in the data.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}
		/** @var WFWorkflow $wfObj */
		$wfObj = $this->wfRepository->findOneBy(['name' => $wfName]);

		if (!$wfObj) {
			$msg = "Workflow with name $wfName was not found.";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		if (!$wfObj->getParameters()) {
			$msg = "Workflow params is needed for $wfName workflow.";
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		/** @var WFParams $params */
		$wfParams = $this->prepareParams($object->data, $wfObj->getParameters());
		$workflow = $this->determineWorkflowToRun($object->workflowType);
		if (!$workflow) {
			throw new BadRequestException("Workflow with name $wfName does not exits.");
		}
		$workflow->Run($wfName, $wfParams);

		return true;
	}

	private function enqueueDueError(mixed $object, OutputInterface $output): void
	{
		if ($object->countFailed > RedisClients::DEFAULT_QUEUE_COUNT_FAILURE) {
			$msg = "Postmark Queue for object  from $object->messageFrom and ID $object->messageId exceeded the maximum of allowed  attempts. It will not be added to the queue";
			$this->loggerSrv->addError($msg, [$object]);
			$output->writeln($msg);
		} else {
			++$object->countFailed;
			$msg = "Adding again to queue the Postmark object from=>$object->messageFrom, ID=>$object->messageId, failed=>$object->countFailed";
			$this->loggerSrv->addInfo($msg);
			$output->writeln($msg);
			$position = $this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_POSTMARK_WEBHOOK_QUEUE, serialize($object));
			if ($position < 0) {
				$this->loggerSrv->addError('Unable to Enqueue the payload for Postmark', [
					'From name' => $object->messageFrom,
					'Message ID' => $object->messageId,
					'data' => $object,
				]);
				$output->writeln("Unable to enqueue again the Postmark entity $object->messageFrom and ID $object->messageId");
			}
		}
	}

	/**
	 * @return WFParams
	 */
	private function prepareParams(array $data, WFParams $params)
	{
		$wfParams = new WFParams();
		$wfParams
			->setNotificationType($params->getNotificationType())
			->setNotificationTarget($params->getNotificationTarget())
			->setParams(array_merge($params->getParams(), [
				'data' => $data,
			]));

		return $wfParams;
	}

	private function determineWorkflowToRun(string $type): ?WorkflowInterface
	{
		$startClassName = null;

		switch ($type) {
			case WFWorkflow::TYPE_XTRF_PROJECT:
				$startClassName = Services\XtrfProject\Start::class;
				break;
			case WFWorkflow::TYPE_XTRF_PROJECT_V2:
				$startClassName = Services\XtrfProjectV2\Start::class;
				break;
			case WFWorkflow::TYPE_CREATE_ZIP:
				$startClassName = Services\CreateZip\Start::class;
				break;
			case WFWorkflow::TYPE_XTM_PROJECT:
				$startClassName = Services\XtmProject\Start::class;
				break;
			case WFWorkflow::TYPE_XTM_GITHUB:
				$startClassName = Services\XtmGithub\Start::class;
				break;
			case WFWorkflow::TYPE_EMAIL_PARSING:
				$startClassName = Services\EmailParsing\Start::class;
				break;
			case WFWorkflow::TYPE_XTM_TM:
				$startClassName = Services\XtmTm\Start::class;
				break;
			case WFWorkflow::TYPE_ATTESTATION:
				$startClassName = Services\Attestation\Start::class;
				break;
			case WFWorkflow::TYPE_XTRF_QBO:
				$startClassName = Services\XtrfQbo\Start::class;
				break;
			case WFWorkflow::TYPE_BL_XTRF:
				$startClassName = Services\BlXtrf\Start::class;
				break;
		}

		if (!$startClassName) {
			return null;
		}

		return $this->workflowSrvFactory->getStartClass($startClassName);
	}
}
