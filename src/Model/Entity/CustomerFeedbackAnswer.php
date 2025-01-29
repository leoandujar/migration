<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_feedback_answer')]
#[ORM\Entity]
class CustomerFeedbackAnswer implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_feedback_answer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_feedback_answer_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'value', type: 'text', nullable: true)]
	private ?string $value;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: false)]
	private Project $project;

	#[ORM\ManyToOne(targetEntity: CustomerFeedbackQuestion::class)]
	#[ORM\JoinColumn(name: 'customer_feedback_question_id', referencedColumnName: 'customer_feedback_question_id', nullable: false)]
	private CustomerFeedbackQuestion $customerFeedbackQuestion;

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

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(?string $value): self
	{
		$this->value = $value;

		return $this;
	}

	public function getProject(): ?Project
	{
		return $this->project;
	}

	public function setProject(?Project $project): self
	{
		$this->project = $project;

		return $this;
	}

	public function getCustomerFeedbackQuestion(): ?CustomerFeedbackQuestion
	{
		return $this->customerFeedbackQuestion;
	}

	public function setCustomerFeedbackQuestion(?CustomerFeedbackQuestion $customerFeedbackQuestion): self
	{
		$this->customerFeedbackQuestion = $customerFeedbackQuestion;

		return $this;
	}
}
