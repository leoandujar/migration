<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'workflow')]
#[ORM\UniqueConstraint(name: 'workflow_name_key', columns: ['name'])]
#[ORM\Entity]
class Workflow implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'workflow_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTime $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'auto_convert_quote_accepted_by_customer', type: 'boolean', nullable: true)]
	private ?bool $autoConvertQuoteAcceptedByCustomer;

	#[ORM\Column(name: 'auto_send_quote_for_customer_confirmation', type: 'boolean', nullable: true)]
	private ?bool $autoSendQuoteForCustomerConfirmation;

	#[ORM\Column(name: 'description', type: 'string', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'mt_engine', type: 'string', length: 4095, nullable: true)]
	private ?string $mtEngine;

	#[ORM\Column(name: 'is_task_invoiceable', type: 'boolean', nullable: true)]
	private ?bool $isTaskInvoiceable;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'default_task_workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private Workflow $defaultTaskWorkflow;

	#[ORM\Column(name: 'workflow_definition_id', type: 'bigint', nullable: true)]
	private ?string $workflowDefinitionId;

	#[ORM\Column(name: 'external_system_id', type: 'bigint', nullable: true)]
	private ?string $externalSystemId;

	#[ORM\Column(name: 'workflow_meta_directories_id', type: 'bigint', nullable: true)]
	private ?string $workflowMetaDirectoriesId;

	#[ORM\Column(name: 'standard_property_container_id', type: 'bigint', nullable: true)]
	private ?string $standardPropertyContainerId;

	#[ORM\Column(name: 'provider_selection_settings_id', type: 'bigint', nullable: true)]
	private ?string $providerSelectionSettingsId;

	#[ORM\Column(name: 'allow_customer_to_access_files_in_language_dependent_tasks', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $allowCustomerToAccessFilesInLanguageDependentTasks;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	public function setActive(?bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function getDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	public function getAutoConvertQuoteAcceptedByCustomer(): ?bool
	{
		return $this->autoConvertQuoteAcceptedByCustomer;
	}

	public function setAutoConvertQuoteAcceptedByCustomer(?bool $autoConvertQuoteAcceptedByCustomer): self
	{
		$this->autoConvertQuoteAcceptedByCustomer = $autoConvertQuoteAcceptedByCustomer;

		return $this;
	}

	public function getAutoSendQuoteForCustomerConfirmation(): ?bool
	{
		return $this->autoSendQuoteForCustomerConfirmation;
	}

	public function setAutoSendQuoteForCustomerConfirmation(?bool $autoSendQuoteForCustomerConfirmation): self
	{
		$this->autoSendQuoteForCustomerConfirmation = $autoSendQuoteForCustomerConfirmation;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getMtEngine(): ?string
	{
		return $this->mtEngine;
	}

	public function setMtEngine(?string $mtEngine): self
	{
		$this->mtEngine = $mtEngine;

		return $this;
	}

	public function getIsTaskInvoiceable(): ?bool
	{
		return $this->isTaskInvoiceable;
	}

	public function setIsTaskInvoiceable(?bool $isTaskInvoiceable): self
	{
		$this->isTaskInvoiceable = $isTaskInvoiceable;

		return $this;
	}

	public function getWorkflowDefinitionId(): ?string
	{
		return $this->workflowDefinitionId;
	}

	public function setWorkflowDefinitionId(?string $workflowDefinitionId): self
	{
		$this->workflowDefinitionId = $workflowDefinitionId;

		return $this;
	}

	public function getExternalSystemId(): ?string
	{
		return $this->externalSystemId;
	}

	public function setExternalSystemId(?string $externalSystemId): self
	{
		$this->externalSystemId = $externalSystemId;

		return $this;
	}

	public function getWorkflowMetaDirectoriesId(): ?string
	{
		return $this->workflowMetaDirectoriesId;
	}

	public function setWorkflowMetaDirectoriesId(?string $workflowMetaDirectoriesId): self
	{
		$this->workflowMetaDirectoriesId = $workflowMetaDirectoriesId;

		return $this;
	}

	public function getStandardPropertyContainerId(): ?string
	{
		return $this->standardPropertyContainerId;
	}

	public function setStandardPropertyContainerId(?string $standardPropertyContainerId): self
	{
		$this->standardPropertyContainerId = $standardPropertyContainerId;

		return $this;
	}

	public function getProviderSelectionSettingsId(): ?string
	{
		return $this->providerSelectionSettingsId;
	}

	public function setProviderSelectionSettingsId(?string $providerSelectionSettingsId): self
	{
		$this->providerSelectionSettingsId = $providerSelectionSettingsId;

		return $this;
	}

	public function getAllowCustomerToAccessFilesInLanguageDependentTasks(): ?bool
	{
		return $this->allowCustomerToAccessFilesInLanguageDependentTasks;
	}

	public function setAllowCustomerToAccessFilesInLanguageDependentTasks(bool $allowCustomerToAccessFilesInLanguageDependentTasks): self
	{
		$this->allowCustomerToAccessFilesInLanguageDependentTasks = $allowCustomerToAccessFilesInLanguageDependentTasks;

		return $this;
	}

	public function getDefaultTaskWorkflow(): ?self
	{
		return $this->defaultTaskWorkflow;
	}

	public function setDefaultTaskWorkflow(?self $defaultTaskWorkflow): self
	{
		$this->defaultTaskWorkflow = $defaultTaskWorkflow;

		return $this;
	}
}
