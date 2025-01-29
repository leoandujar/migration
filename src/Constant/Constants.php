<?php

namespace App\Constant;

use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\BlProviderInvoice;
use App\Model\Entity\WFParams;
use App\Model\Entity\WFWorkflow;
use App\Service\XtrfWebhookService;

class Constants
{
	public static function getWfDisks(): array
	{
		return [
			WFParams::DISK_AWS_INVOICES,
			WFParams::DISK_AWS_PROJECTS,
			WFParams::DISK_AWS_SMARTFOLDERS,
			WFParams::DISK_AZ_WORKFLOW,
			WFParams::DISK_AZ_FILE_STORAGE,
			WFParams::DISK_LOCAL,
		];
	}

	public static function getWfNotificationTypes(): array
	{
		return [
			WFParams::NOTIFICATION_TYPE_TEAM,
			WFParams::NOTIFICATION_TYPE_EMAIL,
			WFParams::NOTIFICATION_TYPE_SMS,
		];
	}

	public static function getWfTypes(): array
	{
		return [
			WFWorkflow::TYPE_XTRF_PROJECT,
			WFWorkflow::TYPE_CREATE_ZIP,
			WFWorkflow::TYPE_XTM_PROJECT,
			WFWorkflow::TYPE_XTM_GITHUB,
			WFWorkflow::TYPE_EMAIL_PARSING,
			WFWorkflow::TYPE_XTM_TM,
			WFWorkflow::TYPE_ATTESTATION,
			WFWorkflow::TYPE_FTP_XTRF,
			WFWorkflow::TYPE_XTRF_FTP,
		];
	}

	public static function getWorkflowMonitorStatus(): array
	{
		return [
			AVWorkflowMonitor::STATUS_PENDING,
			AVWorkflowMonitor::STATUS_RUNNING,
			AVWorkflowMonitor::STATUS_FINISHED,
			AVWorkflowMonitor::STATUS_FAILED,
		];
	}

	public static function getBoostlingProviderInvoiceStatusMap(): array
	{
		return [
			1 => BlProviderInvoice::STATUS_DRAFT,
			4 => BlProviderInvoice::STATUS_APPROVED,
			7 => BlProviderInvoice::STATUS_PAID,
			8 => BlProviderInvoice::STATUS_VOIDED,
			9 => BlProviderInvoice::STATUS_ARCHIVED,
		];
	}

	public static function getXtrfSubscriptionTypeList(): array
	{
		return [
			XtrfWebhookService::EVENT_TASKS_FILES_READY => 'Files ready',
			XtrfWebhookService::EVENT_PROJECT_CREATED => 'Project created',
			XtrfWebhookService::EVENT_PROJECT_STATUS_CHANGED => 'Project status changed',
			XtrfWebhookService::EVENT_QUOTE_CREATED => 'Quote created',
			XtrfWebhookService::EVENT_QUOTE_STATUS_CHANGED => 'Quote status changed',
			XtrfWebhookService::EVENT_JOB_STATUS_CHANGED => 'Job status changed',
			XtrfWebhookService::EVENT_CUSTOMER_CREATED => 'Customer created',
			XtrfWebhookService::EVENT_CUSTOMER_UPDATED => 'Customer updated',
		];
	}
}
