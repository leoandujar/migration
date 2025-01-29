<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_rate')]
#[ORM\Index(name: '', columns: ['blrate_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['blrate_id'])]
#[ORM\Entity]
class BlRate implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_rate_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_rate_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blrate_id', type: 'bigint', nullable: false)]
	private string $blRateId;

	#[ORM\Column(name: 'enabled', type: 'boolean', nullable: true)]
	private ?bool $enabled;

	#[ORM\Column(name: 'communication_type_id', type: 'integer', nullable: false)]
	private int $communicationTypeId;

	#[ORM\Column(name: 'source_language_id', type: 'integer', nullable: false)]
	private int $sourceLanguageId;

	#[ORM\Column(name: 'target_language_id', type: 'integer', nullable: false)]
	private int $targetLanguageId;

	/**
	 * @var float
	 */
	#[ORM\Column(name: 'rate', type: 'decimal', precision: 19, scale: 6, nullable: false)]
	private float $rate;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlRateId(): ?int
	{
		return $this->blRateId;
	}

	public function setBlRateId(int $blRateId): self
	{
		$this->blRateId = $blRateId;

		return $this;
	}

	public function getEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(?bool $enabled): self
	{
		$this->enabled = $enabled;

		return $this;
	}

	public function getCommunicationTypeId(): ?int
	{
		return $this->communicationTypeId;
	}

	public function setCommunicationTypeId(int $communicationTypeId): self
	{
		$this->communicationTypeId = $communicationTypeId;

		return $this;
	}

	public function getSourceLanguageId(): ?int
	{
		return $this->sourceLanguageId;
	}

	public function setSourceLanguageId(int $sourceLanguageId): self
	{
		$this->sourceLanguageId = $sourceLanguageId;

		return $this;
	}

	public function getTargetLanguageId(): ?int
	{
		return $this->targetLanguageId;
	}

	public function setTargetLanguageId(int $targetLanguageId): self
	{
		$this->targetLanguageId = $targetLanguageId;

		return $this;
	}

	public function getRate(): ?float
	{
		return $this->rate;
	}

	public function setRate(float $rate): self
	{
		$this->rate = $rate;

		return $this;
	}
}
