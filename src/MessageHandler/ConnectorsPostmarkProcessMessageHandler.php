<?php

namespace App\MessageHandler;

use App\Message\ConnectorsPostmarkProcessMessage;
use App\Model\Entity\InternalUser;
use App\Model\Repository\WorkflowRepository;
use App\Workflow\Services\WorkflowInterface;
use App\Workflow\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Service\LoggerService;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFWorkflow;
use App\Service\Notification\NotificationService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Workflow\WorkflowServiceFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ConnectorsPostmarkProcessMessageHandler
{
	public const TYPE_POSTMARK_EMAIL_WEBHOOK = 1;
	public const TYPE_POSTMARK_EMAIL_SMS = 2;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private NotificationService $notificationSrv;
	private WorkflowServiceFactory $workflowSrvFactory;
	private MessageBusInterface $bus;

	public function __construct(
		LoggerService $loggerSrv,
		NotificationService $notificationSrv,
		WorkflowServiceFactory $workflowSrvFactory,
		EntityManagerInterface $em,
		MessageBusInterface $bus,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->notificationSrv = $notificationSrv;
		$this->workflowSrvFactory = $workflowSrvFactory;
		$this->em = $em;
		$this->bus = $bus;
	}

	public function __invoke(ConnectorsPostmarkProcessMessage $message): void
	{
		$dequeueLimit = null != $message->getDequeueLimit() ? $message->getDequeueLimit() : 10000;
		do {
			$totalProcessed = 0;
			$dataPostmark = $message->getData();
			while ($dequeueLimit-- > 0) {
				if (null === $dataPostmark) {
					$msg = 'Postmark queue is empty.';
					$this->loggerSrv->addWarning($msg);
					$dequeueLimit = 0;
				}
				try {
					if (($postmarkObj = unserialize($dataPostmark)) === false || !is_object($postmarkObj)) {
						throw new \Exception("Unable to unserialize payload with data $dataPostmark");
					}

					$processedResponse = match ($postmarkObj->type) {
						self::TYPE_POSTMARK_EMAIL_WEBHOOK => $this->processWebhook($postmarkObj),
						self::TYPE_POSTMARK_EMAIL_SMS => $this->processSms($postmarkObj),
					};

					++$totalProcessed;
					if (true !== $processedResponse) {
						--$totalProcessed;
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Postmark object from queue. Check logs for more details.', $thr);
					$data = (object) [
						'countFailed' => 0,
						'data' => $postmarkObj,
					];
					$this->enqueueDueError($data);
					--$totalProcessed;
					continue;
				}
			}
		} while (0);
		$this->loggerSrv->addInfo(sprintf('TOTAL PROCESSED=> %s ROWS.', $totalProcessed));
	}

	private function processSms(mixed $object): bool
	{
		$smsText = $object->smsText;
		$tag = $object->workflowName;
		if (empty($smsText) || empty($tag)) {
			$this->loggerSrv->addError('Tag or SmsText empty for postmark SMS process');

			return false;
		}
		$mobiles = $this->em->getRepository(InternalUser::class)->findByTag($tag);
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
		$wfObj = $this->em->getRepository(WorkflowRepository::class)->findOneBy(['name' => $wfName]);

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

	private function enqueueDueError(mixed $object): void
	{
		if ($object->countFailed > 10) {
			$msg = "Postmark message for object  from $object->messageFrom and ID $object->messageId exceeded the maximum of allowed  attempts";
			$this->loggerSrv->addError($msg, [$object]);
		} else {
			++$object->countFailed;
			$msg = "Retrying to send message to the Postmark object from=>$object->messageFrom, ID=>$object->messageId, failed=>$object->countFailed";
			$this->loggerSrv->addInfo($msg);
			$this->bus->dispatch(new ConnectorsPostmarkProcessMessage(data:$object));
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
