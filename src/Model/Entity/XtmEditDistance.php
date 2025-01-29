<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'xtm_edit_distance')]
#[ORM\Entity]
class XtmEditDistance implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'xtm_edit_distance_id', type: 'guid')]
	private string $id;

	#[ORM\OneToOne(targetEntity: AnalyticsProject::class, cascade: ['persist'], inversedBy: 'xtmEditDistance')]
	#[ORM\JoinColumn(name: 'analytics_project_id', nullable: false)]
	private AnalyticsProject $analyticsProject;

	#[ORM\Column(name: 'rows_count', type: 'integer', nullable: false)]
	private int $rowsCount = 0;

	#[ORM\Column(name: 'rows_zero_count', type: 'integer', nullable: false)]
	private int $rowsZeroCount = 0;

	#[ORM\Column(name: 'lower_score', type: 'decimal', precision: 19, scale: 15, nullable: false)]
	private float $lowerScore = 0;

	#[ORM\Column(name: 'higher_score', type: 'decimal', precision: 19, scale: 15, nullable: false)]
	private float $higherScore = 0;

	#[ORM\Column(name: 'average_score', type: 'decimal', precision: 19, scale: 15, nullable: false)]
	private float $averageScore = 0;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getRowsCount(): ?int
	{
		return $this->rowsCount;
	}

	public function setRowsCount(int $rowsCount): self
	{
		$this->rowsCount = $rowsCount;

		return $this;
	}

	public function getRowsZeroCount(): ?int
	{
		return $this->rowsZeroCount;
	}

	public function setRowsZeroCount(int $rowsZeroCount): self
	{
		$this->rowsZeroCount = $rowsZeroCount;

		return $this;
	}

	public function getLowerScore(): ?string
	{
		return $this->lowerScore;
	}

	public function setLowerScore(string $lowerScore): self
	{
		$this->lowerScore = $lowerScore;

		return $this;
	}

	public function getHigherScore(): ?string
	{
		return $this->higherScore;
	}

	public function setHigherScore(string $higherScore): self
	{
		$this->higherScore = $higherScore;

		return $this;
	}

	public function getAverageScore(): ?string
	{
		return $this->averageScore;
	}

	public function setAverageScore(string $averageScore): self
	{
		$this->averageScore = $averageScore;

		return $this;
	}

	public function getAnalyticsProject(): ?AnalyticsProject
	{
		return $this->analyticsProject;
	}

	public function setAnalyticsProject(AnalyticsProject $analyticsProject): self
	{
		$this->analyticsProject = $analyticsProject;

		return $this;
	}
}
