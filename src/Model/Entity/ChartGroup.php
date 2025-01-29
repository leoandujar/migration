<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'chart_group')]
#[ORM\Entity]
class ChartGroup
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'chart_group_id', type: 'guid')]
	private string $id;

	#[ORM\Column(name: 'code', type: 'string', nullable: false, unique: true)]
	private string $code;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 'true'])]
	private bool $active;

	#[ORM\ManyToMany(targetEntity: AVChart::class, mappedBy: 'groups', cascade: ['persist'])]
	private mixed $charts;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->charts = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	/**
	 * @return mixed
	 */
	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getCharts(): Collection
	{
		return $this->charts;
	}

	/**
	 * @return mixed
	 */
	public function addChart(AVChart $chart): self
	{
		if (!$this->charts->contains($chart)) {
			$this->charts[] = $chart;
			$chart->addGroup($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeChart(AVChart $chart): self
	{
		if ($this->charts->contains($chart)) {
			$this->charts->removeElement($chart);
			$chart->removeGroup($this);
		}

		return $this;
	}

	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}
}
