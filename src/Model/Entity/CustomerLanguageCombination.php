<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'customer_language_combination')]
#[ORM\UniqueConstraint(name: 'customer_language_combination_customer_id_source_language_i_key', columns: ['customer_id', 'source_language_id', 'target_language_id'])]
#[ORM\Entity]
class CustomerLanguageCombination implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_language_combination_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_language_combination_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\JoinTable(name: 'customer_language_combination_specializations')]
	#[ORM\JoinColumn(name: 'customer_language_combination_id', referencedColumnName: 'customer_language_combination_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'language_specialization_id', referencedColumnName: 'language_specialization_id')]
	#[ORM\ManyToMany(targetEntity: LanguageSpecialization::class, cascade: ['persist'], inversedBy: 'customerLanguageCombinations')]
	protected mixed $languagesSpecialization;

	public function __construct()
	{
		$this->languagesSpecialization = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

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

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	/**
	 * @return mixed
	 */
	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getLanguagesSpecialization(): Collection
	{
		return $this->languagesSpecialization;
	}

	/**
	 * @return mixed
	 */
	public function addLanguagesSpecialization(LanguageSpecialization $languagesSpecialization): self
	{
		if (!$this->languagesSpecialization->contains($languagesSpecialization)) {
			$this->languagesSpecialization[] = $languagesSpecialization;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeLanguagesSpecialization(LanguageSpecialization $languagesSpecialization): self
	{
		if ($this->languagesSpecialization->contains($languagesSpecialization)) {
			$this->languagesSpecialization->removeElement($languagesSpecialization);
		}

		return $this;
	}
}
