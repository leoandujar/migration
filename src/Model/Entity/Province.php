<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'province')]
#[ORM\UniqueConstraint(name: 'province_name_country_id_key', columns: ['name', 'country_id'])]
#[ORM\Entity]
class Province implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'province_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'province_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'provinces')]
	#[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'country_id', nullable: false)]
	private Country $country;

	#[ORM\Column(name: 'symbol', type: 'text', nullable: true)]
	private ?string $symbol;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

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
	public function setSymbol(?string $symbol): self
	{
		$this->symbol = $symbol;

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
	public function getCountry(): ?Country
	{
		return $this->country;
	}

	/**
	 * @return mixed
	 */
	public function setCountry(?Country $country): self
	{
		$this->country = $country;

		return $this;
	}
}
