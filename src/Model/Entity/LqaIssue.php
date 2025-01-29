<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'xtm_lqa_issue')]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\LqaIssueRepository')]
class LqaIssue implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'lqa_issue_id', type: 'guid')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: AnalyticsProject::class, inversedBy: 'lqaIssues')]
	#[ORM\JoinColumn(nullable: false)]
	private AnalyticsProject $analyticsProject;

	#[ORM\ManyToOne(targetEntity: LqaIssueTypeMapping::class)]
	#[ORM\JoinColumn(name: 'lqa_issue_type_mapping_id', referencedColumnName: 'lqa_issue_type_mapping_id', nullable: true)]
	private ?LqaIssueTypeMapping $lqaIssueTypeMapping;

	#[ORM\Column(type: 'smallint')]
	private int $weight;

	#[ORM\Column(type: 'smallint')]
	private int $neutral;

	#[ORM\Column(type: 'smallint')]
	private int $minor;

	#[ORM\Column(type: 'smallint')]
	private int $major;

	#[ORM\Column(type: 'smallint')]
	private int $critical;

	#[ORM\Column(type: 'integer', options: ['default' => 0])]
	private int $penaltyRaw;

	#[ORM\Column(type: 'integer', options: ['default' => 0])]
	private int $penaltyAdjusted;

	#[ORM\Column(type: 'decimal', precision: 5, scale: 2, options: ['default' => '0.00'])]
	private float $targetSubscore;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getAnalyticsProject(): ?AnalyticsProject
	{
		return $this->analyticsProject;
	}

	public function setAnalyticsProject(?AnalyticsProject $analyticsProject): self
	{
		$this->analyticsProject = $analyticsProject;

		return $this;
	}

	public function getLqaIssueTypeMapping(): ?LqaIssueTypeMapping
	{
		return $this->lqaIssueTypeMapping;
	}

	public function setLqaIssueTypeMapping(?LqaIssueTypeMapping $lqaIssueTypeMapping): self
	{
		$this->lqaIssueTypeMapping = $lqaIssueTypeMapping;

		return $this;
	}

	public function getWeight(): ?int
	{
		return $this->weight;
	}

	public function setWeight(int $weight): self
	{
		$this->weight = $weight;

		return $this;
	}

	public function getNeutral(): ?int
	{
		return $this->neutral;
	}

	public function setNeutral(int $neutral): self
	{
		$this->neutral = $neutral;

		return $this;
	}

	public function getMinor(): ?int
	{
		return $this->minor;
	}

	public function setMinor(int $minor): self
	{
		$this->minor = $minor;

		return $this;
	}

	public function getMajor(): ?int
	{
		return $this->major;
	}

	public function setMajor(int $major): self
	{
		$this->major = $major;

		return $this;
	}

	public function getCritical(): ?int
	{
		return $this->critical;
	}

	public function setCritical(int $critical): self
	{
		$this->critical = $critical;

		return $this;
	}

	public function getPenaltyRaw(): ?int
	{
		return $this->penaltyRaw;
	}

	public function setPenaltyRaw(int $penaltyRaw): self
	{
		$this->penaltyRaw = $penaltyRaw;

		return $this;
	}

	public function getPenaltyAdjusted(): ?int
	{
		return $this->penaltyAdjusted;
	}

	public function setPenaltyAdjusted(int $penaltyAdjusted): self
	{
		$this->penaltyAdjusted = $penaltyAdjusted;

		return $this;
	}

	public function getTargetSubscore(): mixed
	{
		return $this->targetSubscore;
	}

	public function setTargetSubscore($targetSubscore): self
	{
		$this->targetSubscore = $targetSubscore;

		return $this;
	}
}
