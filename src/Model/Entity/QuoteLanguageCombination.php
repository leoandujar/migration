<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'quote_language_combination')]
#[ORM\UniqueConstraint(name: 'quote_unique_combination', columns: ['quote_id', 'source_language_id', 'target_language_id'])]
#[ORM\Entity]
class QuoteLanguageCombination implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'quote_language_combination_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'quote_language_combination_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'languagesCombinations')]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', nullable: false)]
	private Quote $quote;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

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

	public function getQuote(): ?Quote
	{
		return $this->quote;
	}

	public function setQuote(?Quote $quote): self
	{
		$this->quote = $quote;

		return $this;
	}

	public function getSourceLanguage(): ?XtrfLanguage
	{
		return $this->sourceLanguage;
	}

	public function setSourceLanguage(?XtrfLanguage $sourceLanguage): self
	{
		$this->sourceLanguage = $sourceLanguage;

		return $this;
	}

	public function getTargetLanguage(): ?XtrfLanguage
	{
		return $this->targetLanguage;
	}

	public function setTargetLanguage(?XtrfLanguage $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}
}
