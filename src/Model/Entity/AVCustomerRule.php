<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'av_customer_rule')]
#[ORM\Entity]
class AVCustomerRule
{
	#[ORM\Id]
	#[ORM\Column(name: 'av_customer_rule_id', type: 'string', length: 36, unique: true)]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'event', type: 'string', length: 50, nullable: false)]
	private string $event;

	#[ORM\Column(name: 'type', type: 'string', length: 30, nullable: false)]
	private string $type;

	#[ORM\Column(name: 'filters', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $filters;

	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'rules')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: WFWorkflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'wf_workflow_id', nullable: true)]
	private ?WFWorkflow $workflow;

	#[ORM\Column(name: 'parameters', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $parameters;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->workflow = null;
		$this->filters = [];
		$this->parameters = [];
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getEvent(): ?string
	{
		return $this->event;
	}

	public function setEvent(string $event): static
	{
		$this->event = $event;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(string $type): static
	{
		$this->type = $type;

		return $this;
	}

	public function getFilters(): ?array
	{
		return $this->filters;
	}

	public function setFilters(?array $filters): static
	{
		$this->filters = $filters;

		return $this;
	}

	public function getParameters(): ?array
	{
		return $this->parameters;
	}

	public function setParameters(?array $parameters): static
	{
		$this->parameters = $parameters;

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): static
	{
		$this->customer = $customer;

		return $this;
	}

	public function getWorkflow(): ?WFWorkflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?WFWorkflow $workflow): static
	{
		$this->workflow = $workflow;

		return $this;
	}
}
