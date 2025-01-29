<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'av_dashboard')]
#[ORM\Entity]
class AVDashboard implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'av_dashboard_id', type: 'guid')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: ContactPerson::class, inversedBy: 'dashboard')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ContactPerson $contactPerson;

	#[ORM\ManyToOne(targetEntity: AVChart::class, inversedBy: 'avDashboard')]
	#[ORM\JoinColumn(name: 'av_report_chart_id', referencedColumnName: 'av_report_chart_id', nullable: true)]
	private AVChart $avChart;

	#[ORM\Column(name: 'options', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $options;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getOptions(): ?array
	{
		return $this->options;
	}

	public function setOptions(?array $options): static
	{
		$this->options = $options;

		return $this;
	}

	public function getContactPerson(): ?ContactPerson
	{
		return $this->contactPerson;
	}

	public function setContactPerson(?ContactPerson $contactPerson): static
	{
		$this->contactPerson = $contactPerson;

		return $this;
	}

	public function getAvChart(): ?AVChart
	{
		return $this->avChart;
	}

	public function setAvChart(?AVChart $avChart): static
	{
		$this->avChart = $avChart;

		return $this;
	}
}
