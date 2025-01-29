<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_report_history')]
#[ORM\Entity]
class AVReportHistory
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'av_report_history_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'av_report_history_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: AVReportTemplate::class)]
	#[ORM\JoinColumn(name: 'av_report_template_id', referencedColumnName: 'av_report_template_id', nullable: false)]
	private AVReportTemplate $template;

	#[ORM\ManyToOne(targetEntity: ContactPerson::class)]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: false)]
	private ContactPerson $contactPerson;

	#[ORM\Column(name: 'created', type: 'datetime', nullable: false)]
	private \DateTimeInterface $created;

	public function __construct()
	{
		$this->created = new \DateTime();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getCreated(): ?\DateTimeInterface
	{
		return $this->created;
	}

	public function setCreated(\DateTimeInterface $created): self
	{
		$this->created = $created;

		return $this;
	}

	public function getTemplate(): ?AVReportTemplate
	{
		return $this->template;
	}

	public function setTemplate(?AVReportTemplate $template): self
	{
		$this->template = $template;

		return $this;
	}

	public function getContactPerson(): ?ContactPerson
	{
		return $this->contactPerson;
	}

	public function setContactPerson(?ContactPerson $contactPerson): self
	{
		$this->contactPerson = $contactPerson;

		return $this;
	}
}
