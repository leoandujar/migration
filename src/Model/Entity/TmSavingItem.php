<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'tm_savings_item')]
#[ORM\Entity]
class TmSavingItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'tm_savings_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'tm_savings_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'fixed_rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $fixedRate;

	#[ORM\Column(name: 'match_type', type: 'string', nullable: false)]
	private string $matchType;

	#[ORM\Column(name: 'percentage_rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $percentageRate;

	#[ORM\Column(name: 'quantity', type: 'decimal', precision: 19, scale: 3, nullable: false)]
	private float $quantity;

	#[ORM\ManyToOne(targetEntity: TmSaving::class)]
	#[ORM\JoinColumn(name: 'tm_savings_id', referencedColumnName: 'tm_savings_id', nullable: false)]
	private TmSaving $tmSaving;

	#[ORM\Column(name: 'old_quantity', type: 'decimal', precision: 19, scale: 3, nullable: true)]
	private ?float $oldQuantity;

	#[ORM\Column(name: 'old_fixed_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $oldFixedRate;

	#[ORM\Column(name: 'old_percentage_rate', type: 'decimal', precision: 19, scale: 5, nullable: true)]
	private ?float $oldPercentageRate;

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

	public function getFixedRate(): ?string
	{
		return $this->fixedRate;
	}

	public function setFixedRate(string $fixedRate): self
	{
		$this->fixedRate = $fixedRate;

		return $this;
	}

	public function getMatchType(): ?string
	{
		return $this->matchType;
	}

	public function setMatchType(string $matchType): self
	{
		$this->matchType = $matchType;

		return $this;
	}

	public function getPercentageRate(): ?string
	{
		return $this->percentageRate;
	}

	public function setPercentageRate(string $percentageRate): self
	{
		$this->percentageRate = $percentageRate;

		return $this;
	}

	public function getQuantity(): ?string
	{
		return $this->quantity;
	}

	public function setQuantity(string $quantity): self
	{
		$this->quantity = $quantity;

		return $this;
	}

	public function getOldQuantity(): ?string
	{
		return $this->oldQuantity;
	}

	public function setOldQuantity(string $oldQuantity): self
	{
		$this->oldQuantity = $oldQuantity;

		return $this;
	}

	public function getOldFixedRate(): ?string
	{
		return $this->oldFixedRate;
	}

	public function setOldFixedRate(string $oldFixedRate): self
	{
		$this->oldFixedRate = $oldFixedRate;

		return $this;
	}

	public function getOldPercentageRate(): ?string
	{
		return $this->oldPercentageRate;
	}

	public function setOldPercentageRate(string $oldPercentageRate): self
	{
		$this->oldPercentageRate = $oldPercentageRate;

		return $this;
	}

	public function getTmSaving(): ?TmSaving
	{
		return $this->tmSaving;
	}

	public function setTmSaving(?TmSaving $tmSaving): self
	{
		$this->tmSaving = $tmSaving;

		return $this;
	}
}
