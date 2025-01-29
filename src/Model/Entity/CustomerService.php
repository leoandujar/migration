<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'customer_services')]
#[ORM\UniqueConstraint(name: 'customer_services_unique_constraint', columns: ['customer_id', 'service_id'])]
#[ORM\Entity]
class CustomerService implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Service::class, fetch: 'EAGER')]
	#[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'service_id', nullable: false)]
	private Service $service;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	#[ORM\Column(name: 'process_template_id', type: 'bigint', nullable: true)]
	private ?string $processTemplateId;

	public function getProcessTemplateId(): ?string
	{
		return $this->processTemplateId;
	}

	public function setProcessTemplateId(?string $processTemplateId): self
	{
		$this->processTemplateId = $processTemplateId;

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getService(): ?Service
	{
		return $this->service;
	}

	public function setService(?Service $service): self
	{
		$this->service = $service;

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
}
