<?php

namespace App\Model\Entity;

use App\Model\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'country')]
#[ORM\UniqueConstraint(name: 'country_name_key', columns: ['name'])]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'country_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'country_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'symbol', type: 'string', unique: true, nullable: false)]
	private string $symbol;

	#[ORM\Column(name: 'country_specific_tax_no1_code_name', type: 'string', nullable: true)]
	private ?string $countrySpecificTaxNo1CodeName;

	#[ORM\Column(name: 'country_specific_tax_no2_code_name', type: 'string', nullable: true)]
	private ?string $countrySpecificTaxNo2CodeName;

	#[ORM\Column(name: 'fiscal_code_checking_type', type: 'string', nullable: true)]
	private ?string $fiscalCodeCheckingType;

	#[ORM\Column(name: 'country_specific_tax_no3_code_name', type: 'string', nullable: true)]
	private ?string $countrySpecificTaxNo3CodeName;

	#[ORM\OneToMany(targetEntity: Province::class, mappedBy: 'country')]
	private mixed $provinces;

	public function __construct()
	{
		$this->provinces = new ArrayCollection();
	}

	public function __toString()
	{
		return "$this->name";
	}

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
	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(?bool $active): self
	{
		$this->active = $active;

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
	public function getLocalizedEntity(): ?array
	{
		return $this->localizedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setLocalizedEntity(?array $localizedEntity): self
	{
		$this->localizedEntity = $localizedEntity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDefaultEntity(): ?bool
	{
		return $this->defaultEntity;
	}

	/**
	 * @return mixed
	 */
	public function setDefaultEntity(bool $defaultEntity): self
	{
		$this->defaultEntity = $defaultEntity;

		return $this;
	}

	/**
	 * @return mixed
	 */
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

	/**
	 * @return mixed
	 */
	public function getPreferedEntity(): ?bool
	{
		return $this->preferedEntity;
	}

	/**
	 * @return mixed
	 */
	public function setPreferedEntity(bool $preferedEntity): self
	{
		$this->preferedEntity = $preferedEntity;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSymbol(): ?string
	{
		return $this->symbol;
	}

	/**
	 * @return mixed
	 */
	public function setSymbol(string $symbol): self
	{
		$this->symbol = $symbol;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCountrySpecificTaxNo1CodeName(): ?string
	{
		return $this->countrySpecificTaxNo1CodeName;
	}

	/**
	 * @return mixed
	 */
	public function setCountrySpecificTaxNo1CodeName(?string $countrySpecificTaxNo1CodeName): self
	{
		$this->countrySpecificTaxNo1CodeName = $countrySpecificTaxNo1CodeName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCountrySpecificTaxNo2CodeName(): ?string
	{
		return $this->countrySpecificTaxNo2CodeName;
	}

	/**
	 * @return mixed
	 */
	public function setCountrySpecificTaxNo2CodeName(?string $countrySpecificTaxNo2CodeName): self
	{
		$this->countrySpecificTaxNo2CodeName = $countrySpecificTaxNo2CodeName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFiscalCodeCheckingType(): ?string
	{
		return $this->fiscalCodeCheckingType;
	}

	/**
	 * @return mixed
	 */
	public function setFiscalCodeCheckingType(?string $fiscalCodeCheckingType): self
	{
		$this->fiscalCodeCheckingType = $fiscalCodeCheckingType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCountrySpecificTaxNo3CodeName(): ?string
	{
		return $this->countrySpecificTaxNo3CodeName;
	}

	/**
	 * @return mixed
	 */
	public function setCountrySpecificTaxNo3CodeName(?string $countrySpecificTaxNo3CodeName): self
	{
		$this->countrySpecificTaxNo3CodeName = $countrySpecificTaxNo3CodeName;

		return $this;
	}

	/**
	 * @return Collection|Province[]
	 */
	public function getProvinces(): Collection
	{
		return $this->provinces;
	}

	public function addProvince(Province $province): self
	{
		if (!$this->provinces->contains($province)) {
			$this->provinces[] = $province;
			$province->setCountry($this);
		}

		return $this;
	}

	public function removeProvince(Province $province): self
	{
		if ($this->provinces->removeElement($province)) {
			// set the owning side to null (unless already changed)
			if ($province->getCountry() === $this) {
				$province->setCountry(null);
			}
		}

		return $this;
	}
}
