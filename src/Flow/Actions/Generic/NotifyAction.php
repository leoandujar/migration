<?php

/*
 *  - Notify-Action -
 *
 *  Based on some parameter that requires a notification,
 *  this action will build and add it.
 *  The parameters that currently need to be notified are: "projectOrQuotes" and "zipPath".
 *  IMPORTANT: This is probably the most hardcoded and non-generic action, but for now this is how
 *  it is viewed and considered. In the future it may probably be improved and more generic
 *
 *  -> Inputs:
 *     - with-notify: Its a specific key that will determine what key (data) will be used
 *       to build the notification.
 *
 *     - notification_type: The type of notification that will be used to build the notification.
 *      TEAMS, PM_EMAIL, MAILER_EMAIL.
 *
 *     - notification_target: The target of the notification that will be used to build
 *      the notification. In XTRFV2 its used target hook for TEAMS. In Attestation its used
 *      'to' parameter for MAILER_EMAIL. Now, 'to' and 'notification_target' are the same.
 *      And it's called 'notification_target'.
 *
 *  -> Outputs: None.
 *
 */

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NotifyAction extends Action
{
	public const ACTION_DESCRIPTION = 'Notify the user about the records of processes';

	private const TEAMS_NOTIFY = 'team';
	private const PM_EMAIL_NOTIFY = 'pmEmail';
	private const SMS_NOTIFY = 'sms';
	private const MAILER_EMAIL_NOTIFY = 'mailerEmail';

	private const PROJECT_QUOTE = 'projectQuote';
	private const FTP_PROJECTS = 'ftpProjects';
	private const ZIP_PATHS = 'zipPaths';
	private const QBO_INVOICES_ATM = 'qboInvoicesAtm';
	private const XTRF_PROJECTS = 'xtrfProjects';

	public const ACTION_INPUTS = [
		'notificationType' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				self::TEAMS_NOTIFY,
				self::PM_EMAIL_NOTIFY,
				self::SMS_NOTIFY,
				self::MAILER_EMAIL_NOTIFY,
			],
			'description' => 'The type of notification that will be used to build the notification. TEAMS, PM_EMAIL, MAILER_EMAIL',
		],
		'notificationTarget' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string or array',
			'details' => 'You can use a string for one target or an array for multiple targets to be notified',
			'description' => 'The target of the notification that will be used to build the notification.',
		],
		'withNotify' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'select',
			'options' => [
				self::PROJECT_QUOTE,
				self::FTP_PROJECTS,
				self::ZIP_PATHS,
				self::QBO_INVOICES_ATM,
				self::XTRF_PROJECTS,
			],
			'description' => 'Its a specific key that will determine what key (data) will be used to build the notification.',
		],
		'downloadLinkUrl' => [
			'required' => false,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The URL link to download the project or quote',
		],
	];

	public const ACTION_OUTPUTS = null;
	private ParameterBagInterface $parameterBag;
	private NotificationService $notificationService;

	public function __construct(
		LoggerService $loggerSrv,
		ParameterBagInterface $parameterBag,
		NotificationService $notificationService,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->parameterBag = $parameterBag;
		$this->notificationService = $notificationService;
		$this->actionName = 'NotifyAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$notificationType = $this->aux['notificationType'];
		$notificationType = match ($notificationType) {
			self::TEAMS_NOTIFY => 1,
			self::PM_EMAIL_NOTIFY => 2,
			self::SMS_NOTIFY => 3,
			self::MAILER_EMAIL_NOTIFY => 4,
			default => throw new \InvalidArgumentException('Invalid notification type'),
		};
		$notificationTarget = $this->aux['notificationTarget'];
		$withNotify = $this->aux['withNotify'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$notifications = $this->buildNotifications($withNotify, $notificationType);

			foreach ($notifications as $notification) {
				$this->notificationService->addNotification(
					$notificationType,
					$notificationTarget,
					$notification,
					'New notification!'
				);
				$this->loggerSrv->addInfo('[FLOW]: NotifyAction has been added');
			}

			$this->outputs = [];

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}

	private function buildNotifications($with, $type): array
	{
		$notifications = [];
		switch ($with) {
			case self::XTRF_PROJECTS:
				$context = $this->params;
				$notifications[] = [
					'message' => 'Translated PDFs and DONE file ready for download',
					'status' => 'success',
					'date' => (new \DateTimeImmutable())->format('Y-m-d'),
					'title' => 'FLOW: Translated PDFs and DONE file ready for download',
					'facts' => [
						'Order Number' => $context['order_number'],
						'Batch Number' => $context['batch_number'],
						'Return PDFs File' => $context['filesUnZipped'],
						'Return Done File' => basename($context['donePath']),
						'Project' => $context['xtrf_project_id'],
						'Sent for mailing' => $context['forMailing'],
					],
				];

				return $notifications;
			case self::FTP_PROJECTS:
				$projectsOrQuotes = $this->params['projectsOrQuotes'];
				$downloadLink = $this->params['downloadLinkUrl'];
				$context = $this->params;
				foreach ($projectsOrQuotes as $poq) {
					$facts = array_merge([
						'Name' => $context['template']['name'],
						'Order Number' => $context['order_number'],
						'Batch Number' => $context['batch_number'],
						'SLA' => $context['sla'],
						'For Mailing' => $context['stats']['forMailing'],
						'For ADA' => $context['stats']['forADA'],
						'Total Files' => $context['stats']['totalFiles'],
					], $context['stats']['files']);
					$notifications[] = [
						'message' => 'Project created in XTRF from FTP',
						'status' => 'Success',
						'date' => (new \DateTime())->format('Y-m-d'),
						'link' => "$downloadLink{$poq}",
						'title' => 'Project Created!',
						'facts' => $facts,
					];

					return $notifications;
				}
				break;
			case self::PROJECT_QUOTE:
				$projectsOrQuotes = $this->params['projectsOrQuotes'];
				$urlLink = $this->params['downloadLinkUrl'];
				foreach ($projectsOrQuotes as $poq) {
					switch ($type) {
						case NotificationService::NOTIFICATION_TYPE_TEAM:
							$notifications[] = [
								'message' => 'New project or quote in XTRF system',
								'status' => 'Ready',
								'date' => (new \DateTime())->format('Y-m-d'),
								'link' => "$urlLink{$poq->id}",
								'title' => 'New project in XTRF system',
							];
							break;
						case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
							$template = $this->parameterBag->get('app.postmark.tpl_id.workflow');
							$notifications[] = [
								'template' => $template,
								'workflow' => 'A new type of WF',
								'link' => "$urlLink{$poq->id}",
							];
							break;
					}
				}

				return $notifications;
			case self::ZIP_PATHS:
				$zipPath = $this->params['zipPath'];
				$totalProjects = $this->params['totalProjects'] ?? 0;
				$projectIds = $this->params['projectIds'] ?? 0;
				$documentsResult = $this->params['documentsResult'] ?? 0;
				$filesError = $this->params['filesError'] ?? 0;
				$filesOcr = $this->params['filesOcr'] ?? 0;
				if (NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL == $type) {
					$notifications[] = [
						'subject' => 'Attestation files',
						'template' => 'attestation',
						'attachments' => [$zipPath],
						'data' => [
							'totalProjects' => $totalProjects,
							'totalDocs' => count($documentsResult),
							'projectIds' => $projectIds,
							'filesError' => $filesError,
							'filesOcr' => $filesOcr,
						],
					];

					return $notifications;
				}
				$documentsResult = null;
				break;
			case self::QBO_INVOICES_ATM:
				$invoicesError = $this->params['invoicesError'] ?? [];
				$correctlyAttached = $this->params['correctlyAttached'] ?? [];
				$pdfsAttachError = $this->params['pdfsAttachError'] ?? [];
				$csvReportUrl = $this->params['csvReportUrl'] ?? null;
				$csvReport = [];
				if ($csvReportUrl) {
					$csvReport[] = [
						'message' => "Invoice report has been created successfully. You can download it from <a href='$csvReportUrl'>here</a>",
						'status' => 'Success',
						'date' => (new \DateTime())->format('Y-m-d'),
						'title' => 'Invoice report has been created successfully',
						'link' => $csvReportUrl,
					];
				}

				return array_merge($invoicesError, $pdfsAttachError, $correctlyAttached, $csvReport);
		}

		return [];
	}
}
