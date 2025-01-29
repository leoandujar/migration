<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'service')]
#[ORM\UniqueConstraint(name: 'service_name_key', columns: ['name'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\ServiceRepository')]
class Service implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'service_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'service_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'bigint', nullable: false)]
	private string $version;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'custom_field_mappings', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $customFieldMappings;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	#[ORM\Column(name: 'project_type', type: 'string', nullable: false)]
	private string $projectType;

	#[ORM\Column(name: 'process_template_id', type: 'bigint', nullable: true)]
	private ?string $processTemplateId;

	#[ORM\Column(name: 'management_mode', type: 'string', nullable: true)]
	private ?string $managementMode;

	#[ORM\ManyToOne(targetEntity: ActivityType::class)]
	#[ORM\JoinColumn(name: 'activity_type_id', referencedColumnName: 'activity_type_id', nullable: true)]
	private ?ActivityType $activityType;

	#[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'service')]
	private mixed $projects;

	public function __construct()
	{
		$this->projects = new ArrayCollection();
	}

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

	public function getVersion(): ?string
	{
		return $this->version;
	}

	public function setVersion(string $version): self
	{
		$this->version = $version;

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

	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	public function getCustomFieldMappings(): ?array
	{
		return $this->customFieldMappings;
	}

	public function setCustomFieldMappings(?array $customFieldMappings): self
	{
		$this->customFieldMappings = $customFieldMappings;

		return $this;
	}

	public function getProjectType(): ?string
	{
		return $this->projectType;
	}

	public function setProjectType(string $projectType): self
	{
		$this->projectType = $projectType;

		return $this;
	}

	public function getProcessTemplateId(): ?string
	{
		return $this->processTemplateId;
	}

	public function setProcessTemplateId(?string $processTemplateId): self
	{
		$this->processTemplateId = $processTemplateId;

		return $this;
	}

	public function getWorkflow(): ?Workflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?Workflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function getActivityType(): ?ActivityType
	{
		return $this->activityType;
	}

	public function setActivityType(?ActivityType $activityType): self
	{
		$this->activityType = $activityType;

		return $this;
	}

	public function getProjects(): Collection
	{
		return $this->projects;
	}

	public function addProject(Project $project): self
	{
		if (!$this->projects->contains($project)) {
			$this->projects[] = $project;
			$project->setService($this);
		}

		return $this;
	}

	public function removeProject(Project $project): self
	{
		if ($this->projects->contains($project)) {
			$this->projects->removeElement($project);
			// set the owning side to null (unless already changed)
			if ($project->getService() === $this) {
				$project->setService(null);
			}
		}

		return $this;
	}

	public function isActive(): ?bool
	{
		return $this->active;
	}

	public function isDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	public function isPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	public function getManagementMode(): ?string
	{
		return $this->managementMode;
	}

	public function setManagementMode(?string $managementMode): self
	{
		$this->managementMode = $managementMode;

		return $this;
	}
}
