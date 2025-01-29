<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'ap_form')]
#[ORM\Index(name: '', columns: ['ap_form_id'])]
#[ORM\Entity]
class APForm implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'ap_form_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'ap_form_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'internal_user_id', nullable: false)]
	private InternalUser $createdBy;

	#[ORM\ManyToOne(targetEntity: APFormTemplate::class)]
	#[ORM\JoinColumn(name: 'ap_form_template_id', referencedColumnName: 'ap_form_template_id', nullable: false)]
	private APFormTemplate $template;

	#[ORM\Column(name: 'category', type: 'integer', nullable: true)]
	private ?int $category;

	#[ORM\Column(name: 'name', type: 'string', length: 30, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
	private \DateTimeInterface $createdAt;

	#[ORM\Column(name: 'pmk_template_id', type: 'string', length: 30, nullable: false)]
	private string $pmkTemplateId;

	#[ORM\Column(name: 'approvers', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $approvers;

	public function __construct()
	{
		$this->createdAt = new \DateTime('now');
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getCategory(): ?int
	{
		return $this->category;
	}

	public function setCategory(?int $category): self
	{
		$this->category = $category;

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

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getPmkTemplateId(): ?string
	{
		return $this->pmkTemplateId;
	}

	public function setPmkTemplateId(string $pmkTemplateId): self
	{
		$this->pmkTemplateId = $pmkTemplateId;

		return $this;
	}

	public function getCreatedBy(): ?InternalUser
	{
		return $this->createdBy;
	}

	public function setCreatedBy(?InternalUser $createdBy): self
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	public function getApprovers(): ?array
	{
		return $this->approvers ?? [];
	}

	public function setApprovers(?array $approvers): self
	{
		$this->approvers = $approvers;

		return $this;
	}

	public function getTemplate(): ?APFormTemplate
	{
		return $this->template;
	}

	public function setTemplate(?APFormTemplate $template): self
	{
		$this->template = $template;

		return $this;
	}
}
