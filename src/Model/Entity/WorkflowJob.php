<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'workflow_job')]
#[ORM\Entity]
class WorkflowJob implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'workflow_job_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'workflow_job_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTime $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'jobtype', type: 'string', length: 31, nullable: false)]
	private string $jobtype;

	#[ORM\Column(name: 'all_bundles', type: 'boolean', nullable: false)]
	private bool $allBundles;

	#[ORM\Column(name: 'assign_to_current_pm', type: 'boolean', nullable: true)]
	private ?bool $assignToCurrentPm;

	#[ORM\Column(name: 'auto_change_status', type: 'boolean', nullable: true)]
	private ?bool $autoChangeStatus;

	#[ORM\Column(name: 'automatically_assign_all_input_files', type: 'boolean', nullable: true)]
	private ?bool $automaticallyAssignAllInputFiles;

	#[ORM\Column(name: 'bundles_for_output', type: 'string', nullable: false)]
	private string $bundlesForOutput;

	#[ORM\Column(name: 'copy_missing_output_files_from_input_on_finish', type: 'boolean', nullable: true)]
	private ?bool $copyMissingOutputFilesFromInputOnFinish;

	#[ORM\Column(name: 'default_request_deadline_base', type: 'string', nullable: true)]
	private ?string $defaultRequestDeadlineBase;

	#[ORM\Column(name: 'default_request_deadline_days', type: 'integer', nullable: false)]
	private int $defaultRequestDeadlineDays;

	#[ORM\Column(name: 'default_request_deadline_hours', type: 'integer', nullable: false)]
	private int $defaultRequestDeadlineHours;

	#[ORM\Column(name: 'default_request_deadline_use_working', type: 'boolean', nullable: false)]
	private bool $defaultRequestDeadlineUseWorking;

	#[ORM\Column(name: 'estimated_time_weight', type: 'integer', nullable: true)]
	private ?int $estimatedTimeWeight;

	#[ORM\Column(name: 'executed_by_external_system', type: 'boolean', nullable: true)]
	private ?bool $executedByExternalSystem;

	#[ORM\Column(name: 'external_system_role', type: 'string', nullable: true)]
	private ?string $externalSystemRole;

	#[ORM\Column(name: 'job_invoicing_option', type: 'string', nullable: false)]
	private string $jobInvoicingOption;

	#[ORM\Column(name: 'job_position', type: 'integer', nullable: true)]
	private ?int $jobPosition;

	#[ORM\Column(name: 'job_starting_mode', type: 'string', nullable: false)]
	private string $jobStartingMode;

	#[ORM\Column(name: 'mapped_by_external_system_workflow_job', type: 'boolean', nullable: true)]
	private ?bool $mappedByExternalSystemWorkflowJob;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'notify_pm_when_activity_partially_finished', type: 'boolean', nullable: true)]
	private ?bool $notifyPmWhenActivityPartiallyFinished;

	#[ORM\Column(name: 'notify_pm_when_activity_ready', type: 'boolean', nullable: true)]
	private ?bool $notifyPmWhenActivityReady;

	#[ORM\Column(name: 'notify_provider_when_activity_started', type: 'boolean', nullable: true)]
	private ?bool $notifyProviderWhenActivityStarted;

	#[ORM\Column(name: 'payable_quantity', type: 'decimal', precision: 19, scale: 3, nullable: true)]
	private ?float $payableQuantity;

	#[ORM\Column(name: 'payables_option', type: 'string', nullable: false)]
	private string $payablesOption;

	#[ORM\Column(name: 'show_warning_if_no_out_files', type: 'boolean', nullable: true)]
	private ?bool $showWarningIfNoOutFiles;

	#[ORM\Column(name: 'bundle_name_expression', type: 'text', nullable: true)]
	private ?string $bundleNameExpression;

	#[ORM\ManyToOne(targetEntity: ActivityType::class)]
	#[ORM\JoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id', nullable: true)]
	private ?ActivityType $activityType;

	#[ORM\Column(name: 'bundles_meta_directory_id', type: 'bigint', nullable: true)]
	private ?string $bundlesMetaDirectoryId;

	#[ORM\Column(name: 'provider_price_profile_id', type: 'bigint', nullable: true)]
	private ?string $providerPriceProfileId;

	#[ORM\ManyToOne(targetEntity: CalculationUnit::class)]
	#[ORM\JoinColumn(name: 'payable_calculation_unit_id', referencedColumnName: 'calculation_unit_id', nullable: true)]
	private ?CalculationUnit $payableCalculationUnit;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'provider_selection_rules_id', type: 'bigint', nullable: true)]
	private ?string $providerSelectionRulesId;

	#[ORM\Column(name: 'user_defined_activity_partially_finished_email_template_id', type: 'bigint', nullable: true)]
	private ?string $userDefinedActivityPartiallyFinishedEmailTemplateId;

	#[ORM\Column(name: 'user_defined_activity_ready_email_template_id', type: 'bigint', nullable: true)]
	private ?string $userDefinedActivityReadyEmailTemplateId;

	#[ORM\Column(name: 'workflow_definition_id', type: 'bigint', nullable: true)]
	private ?string $workflowDefinitionId;

	#[ORM\Column(name: 'bundle_schema_id', type: 'bigint', nullable: true)]
	private ?string $bundleSchemaId;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'task_workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $taskWorkflow;

	#[ORM\Column(name: 'default_request_deadline_minutes', type: 'integer', nullable: false, options: ['default' => 0])]
	private int $defaultRequestDeadlineMinutes;

	#[ORM\Column(name: 'automatically_send_po_for_status', type: 'string', nullable: true)]
	private ?string $automaticallySendPoForStatus;

	#[ORM\Column(name: 'enable_po_download_for_status', type: 'string', nullable: false)]
	private string $enablePoDownloadForStatus;
}
