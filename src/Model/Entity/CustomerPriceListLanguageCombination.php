<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_price_list_language_combination')]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\CustomerPriceListLanguageCombinationRepository')]
class CustomerPriceListLanguageCombination implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_price_list_language_combination_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_price_list_language_combination_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'minimal_charge', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $minimalCharge;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

	#[ORM\ManyToOne(targetEntity: CustomerPriceList::class, inversedBy: 'priceListLanguageCombination')]
	#[ORM\JoinColumn(name: 'customer_price_list_id', referencedColumnName: 'customer_price_list_id', nullable: false)]
	private CustomerPriceList $customerPriceList;

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

	public function getMinimalCharge(): ?string
	{
		return $this->minimalCharge;
	}

	/**
	 * @return mixed
	 */
	public function setMinimalCharge(?string $minimalCharge): self
	{
		$this->minimalCharge = $minimalCharge;

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

	public function getCustomerPriceList(): ?CustomerPriceList
	{
		return $this->customerPriceList;
	}

	/**
	 * @return mixed
	 */
	public function setCustomerPriceList(?CustomerPriceList $customerPriceList): self
	{
		$this->customerPriceList = $customerPriceList;

		return $this;
	}
}
