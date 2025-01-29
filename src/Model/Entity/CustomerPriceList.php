<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'customer_price_list')]
#[ORM\UniqueConstraint(name: 'customer_price_list_name_key', columns: ['name'])]
#[ORM\Entity]
class CustomerPriceList implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_price_list_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_price_list_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private bool $active;

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

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\OneToMany(targetEntity: CustomerPriceListLanguageCombination::class, mappedBy: 'customerPriceList')]
	private mixed $priceListLanguageCombination;

	#[ORM\OneToMany(targetEntity: CustomerPriceProfile::class, mappedBy: 'priceList')]
	private mixed $priceProfile;

	public function __construct()
	{
		$this->priceListLanguageCombination = new ArrayCollection();
		$this->priceProfile                 = new ArrayCollection();
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

	public function getCurrency(): ?Currency
	{
		return $this->currency;
	}

	/**
	 * @return mixed
	 */
	public function setCurrency(?Currency $currency): self
	{
		$this->currency = $currency;

		return $this;
	}

	public function getPriceListLanguageCombination(): Collection
	{
		return $this->priceListLanguageCombination;
	}

	/**
	 * @return mixed
	 */
	public function addPriceListLanguageCombination(CustomerPriceListLanguageCombination $priceListLanguageCombination): self
	{
		if (!$this->priceListLanguageCombination->contains($priceListLanguageCombination)) {
			$this->priceListLanguageCombination[] = $priceListLanguageCombination;
			$priceListLanguageCombination->setCustomerPriceList($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removePriceListLanguageCombination(CustomerPriceListLanguageCombination $priceListLanguageCombination): self
	{
		if ($this->priceListLanguageCombination->contains($priceListLanguageCombination)) {
			$this->priceListLanguageCombination->removeElement($priceListLanguageCombination);
			// set the owning side to null (unless already changed)
			if ($priceListLanguageCombination->getCustomerPriceList() === $this) {
				$priceListLanguageCombination->setCustomerPriceList(null);
			}
		}

		return $this;
	}

	public function getPriceProfile(): Collection
	{
		return $this->priceProfile;
	}

	/**
	 * @return mixed
	 */
	public function addPriceProfile(CustomerPriceProfile $priceProfile): self
	{
		if (!$this->priceProfile->contains($priceProfile)) {
			$this->priceProfile[] = $priceProfile;
			$priceProfile->setPriceList($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removePriceProfile(CustomerPriceProfile $priceProfile): self
	{
		if ($this->priceProfile->contains($priceProfile)) {
			$this->priceProfile->removeElement($priceProfile);
			// set the owning side to null (unless already changed)
			if ($priceProfile->getPriceList() === $this) {
				$priceProfile->setPriceList(null);
			}
		}

		return $this;
	}
}
