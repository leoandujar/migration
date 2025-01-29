<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'provider_language_combination')]
#[ORM\UniqueConstraint(name: 'provider_language_combination_provider_id_source_language_i_key', columns: ['provider_id', 'source_language_id', 'target_language_id'])]
#[ORM\Entity]
class ProviderLanguageCombination implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_language_combination_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_language_combination_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'native_support', type: 'boolean', nullable: true)]
	private ?bool $nativeSupport;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'languageCombinations')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: false)]
	private Provider $provider;

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getVersion(): ?int
	{
		return $this->version;
	}

	/**
	 * @return mixed
	 */
	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getNativeSupport(): ?bool
	{
		return $this->nativeSupport;
	}

	/**
	 * @return mixed
	 */
	public function setNativeSupport(?bool $nativeSupport): self
	{
		$this->nativeSupport = $nativeSupport;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSourceLanguage(): ?XtrfLanguage
	{
		return $this->sourceLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setSourceLanguage(?XtrfLanguage $sourceLanguage): self
	{
		$this->sourceLanguage = $sourceLanguage;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTargetLanguage(): ?XtrfLanguage
	{
		return $this->targetLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguage(?XtrfLanguage $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getProvider(): ?Provider
	{
		return $this->provider;
	}

	/**
	 * @return mixed
	 */
	public function setProvider(?Provider $provider): self
	{
		$this->provider = $provider;

		return $this;
	}
}
