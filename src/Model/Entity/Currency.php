<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'xtrf_currency')]
#[ORM\UniqueConstraint(name: 'xtrf_currency_name_key', columns: ['name'])]
#[ORM\UniqueConstraint(name: '', columns: ['iso_code'])]
#[ORM\Entity]
class Currency implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'xtrf_currency_id', type: 'bigint')]
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

	#[ORM\Column(name: 'iso_code', type: 'string', nullable: true)]
	private ?string $isoCode;

	#[ORM\Column(name: 'symbol', type: 'string', nullable: false)]
	private string $symbol;

	#[ORM\Column(name: 'exchange_ratio', type: 'decimal', precision: 19, scale: 10, nullable: false)]
	private float $exchangeRatio;

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

	public function getIsoCode(): ?string
	{
		return $this->isoCode;
	}

	/**
	 * @return mixed
	 */
	public function setIsoCode(?string $isoCode): self
	{
		$this->isoCode = $isoCode;

		return $this;
	}

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

	public function getExchangeRatio(): ?string
	{
		return $this->exchangeRatio;
	}

	/**
	 * @return mixed
	 */
	public function setExchangeRatio(string $exchangeRatio): self
	{
		$this->exchangeRatio = $exchangeRatio;

		return $this;
	}
}
