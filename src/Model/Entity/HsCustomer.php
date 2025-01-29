<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_customer')]
#[ORM\Entity]
class HsCustomer implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_customer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_customer_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hscustomer_id', type: 'bigint', nullable: false)]
	private string $hsCustomerId;

	#[ORM\OneToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'hsCustomers')]
	#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $owner;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'last_activity_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastActivityDate;

	#[ORM\Column(name: 'first_deal_created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstDealCreatedDate;

	#[ORM\Column(name: 'first_conversion_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstConversionDate;

	#[ORM\Column(name: 'industry', type: 'string', nullable: true)]
	private ?string $industry;

	#[ORM\Column(name: 'lifecicle_stage', type: 'string', nullable: true)]
	private ?string $lifecicleStage;

	#[ORM\Column(name: 'likelihood_to_close', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $likelihoodToClose;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'open_deals', type: 'integer', nullable: true)]
	private ?int $openDeals;

	#[ORM\Column(name: 'contacted', type: 'integer', nullable: true)]
	private ?int $contacted;

	#[ORM\Column(name: 'city', type: 'string', nullable: true)]
	private ?string $city;

	#[ORM\Column(name: 'state', type: 'string', nullable: true)]
	private ?string $state;

	#[ORM\Column(name: 'country', type: 'string', nullable: true)]
	private ?string $country;

	#[ORM\Column(name: 'total_deal_value', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $totalDealValue;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'visits', type: 'integer', nullable: true)]
	private ?int $visits;

	#[ORM\Column(name: 'source_data', type: 'string', nullable: true)]
	private ?string $sourceData;

	#[ORM\Column(name: 'source_type', type: 'string', nullable: true)]
	private ?string $sourceType;

	#[ORM\Column(name: 'sale_type', type: 'string', nullable: true)]
	private ?string $saleType;

	#[ORM\Column(name: 'first_visit', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $firstVisit;

	#[ORM\Column(name: 'last_visit', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastVisit;

	#[ORM\Column(name: 'acquisition_type', type: 'string', nullable: true)]
	private ?string $acquisitionType;

	#[ORM\Column(name: 'form_submissions', type: 'integer', nullable: true)]
	private ?int $formSubmissions;

	#[ORM\Column(name: 'division', type: 'string', nullable: true)]
	private ?string $division;

	#[ORM\Column(name: 'close_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $closeDate;

	#[ORM\Column(name: 'recent_deal_close_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $recentDealCloseDate;

	#[ORM\Column(name: 'current_account_com_status', type: 'string', nullable: true)]
	private ?string $currentAccountComStatus;

	#[ORM\Column(name: 'days_to_close', type: 'string', nullable: true)]
	private ?string $daysToClose;

	#[ORM\Column(name: 'lead_status', type: 'string', nullable: true)]
	private ?string $leadStatus;

	#[ORM\Column(name: 'last_engagement_date', type: 'string', nullable: true)]
	private ?string $lastEngagementDate;

	#[ORM\Column(name: 'last_contacted_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastContactedDate;

	#[ORM\ManyToOne(targetEntity: HsCustomer::class)]
	#[ORM\JoinColumn(name: 'hs_parent_customer_id', referencedColumnName: 'hs_customer_id', nullable: true)]
	private ?HsCustomer $hsParentCustomer;

	#[ORM\Column(name: 'referral_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $referralDate;

	#[ORM\Column(name: 'referral_type', type: 'string', nullable: true)]
	private ?string $referralType;

	#[ORM\Column(name: 'referred_by', type: 'string', nullable: true)]
	private ?string $referredBy;

	#[ORM\Column(name: 'responsible_for_referral', type: 'string', nullable: true)]
	private ?string $responsibleForReferral;

	#[ORM\OneToMany(targetEntity: HsDeal::class, mappedBy: 'hsCustomer', cascade: ['persist'])]
	private ?Collection $hsDeals;

	public function __construct()
	{
		$this->hsDeals = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getHsCustomerId(): ?string
	{
		return $this->hsCustomerId;
	}

	/**
	 * @return mixed
	 */
	public function setHsCustomerId(string $hsCustomerId): self
	{
		$this->hsCustomerId = $hsCustomerId;

		return $this;
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

	public function getCreatedDate(): ?\DateTimeInterface
	{
		return $this->createdDate;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedDate(?\DateTimeInterface $createdDate): self
	{
		$this->createdDate = $createdDate;

		return $this;
	}

	public function getLastActivityDate(): ?\DateTimeInterface
	{
		return $this->lastActivityDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastActivityDate(?\DateTimeInterface $lastActivityDate): self
	{
		$this->lastActivityDate = $lastActivityDate;

		return $this;
	}

	public function getFirstDealCreatedDate(): ?\DateTimeInterface
	{
		return $this->firstDealCreatedDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstDealCreatedDate(?\DateTimeInterface $firstDealCreatedDate): self
	{
		$this->firstDealCreatedDate = $firstDealCreatedDate;

		return $this;
	}

	public function getFirstConversionDate(): ?\DateTimeInterface
	{
		return $this->firstConversionDate;
	}

	/**
	 * @return mixed
	 */
	public function setFirstConversionDate(?\DateTimeInterface $firstConversionDate): self
	{
		$this->firstConversionDate = $firstConversionDate;

		return $this;
	}

	public function getIndustry(): ?string
	{
		return $this->industry;
	}

	/**
	 * @return mixed
	 */
	public function setIndustry(?string $industry): self
	{
		$this->industry = $industry;

		return $this;
	}

	public function getLifecicleStage(): ?string
	{
		return $this->lifecicleStage;
	}

	/**
	 * @return mixed
	 */
	public function setLifecicleStage(?string $lifecicleStage): self
	{
		$this->lifecicleStage = $lifecicleStage;

		return $this;
	}

	public function getLikelihoodToClose(): ?string
	{
		return $this->likelihoodToClose;
	}

	/**
	 * @return mixed
	 */
	public function setLikelihoodToClose(?string $likelihoodToClose): self
	{
		$this->likelihoodToClose = $likelihoodToClose;

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

	public function getOpenDeals(): ?int
	{
		return $this->openDeals;
	}

	/**
	 * @return mixed
	 */
	public function setOpenDeals(?int $openDeals): self
	{
		$this->openDeals = $openDeals;

		return $this;
	}

	public function getContacted(): ?int
	{
		return $this->contacted;
	}

	/**
	 * @return mixed
	 */
	public function setContacted(?int $contacted): self
	{
		$this->contacted = $contacted;

		return $this;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	/**
	 * @return mixed
	 */
	public function setCity(?string $city): self
	{
		$this->city = $city;

		return $this;
	}

	public function getState(): ?string
	{
		return $this->state;
	}

	/**
	 * @return mixed
	 */
	public function setState(?string $state): self
	{
		$this->state = $state;

		return $this;
	}

	public function getCountry(): ?string
	{
		return $this->country;
	}

	/**
	 * @return mixed
	 */
	public function setCountry(?string $country): self
	{
		$this->country = $country;

		return $this;
	}

	public function getTotalDealValue(): ?string
	{
		return $this->totalDealValue;
	}

	/**
	 * @return mixed
	 */
	public function setTotalDealValue(?string $totalDealValue): self
	{
		$this->totalDealValue = $totalDealValue;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getVisits(): ?int
	{
		return $this->visits;
	}

	/**
	 * @return mixed
	 */
	public function setVisits(?int $visits): self
	{
		$this->visits = $visits;

		return $this;
	}

	public function getSourceData(): ?string
	{
		return $this->sourceData;
	}

	/**
	 * @return mixed
	 */
	public function setSourceData(?string $sourceData): self
	{
		$this->sourceData = $sourceData;

		return $this;
	}

	public function getSourceType(): ?string
	{
		return $this->sourceType;
	}

	/**
	 * @return mixed
	 */
	public function setSourceType(?string $sourceType): self
	{
		$this->sourceType = $sourceType;

		return $this;
	}

	public function getFirstVisit(): ?\DateTimeInterface
	{
		return $this->firstVisit;
	}

	/**
	 * @return mixed
	 */
	public function setFirstVisit(?\DateTimeInterface $firstVisit): self
	{
		$this->firstVisit = $firstVisit;

		return $this;
	}

	public function getLastVisit(): ?\DateTimeInterface
	{
		return $this->lastVisit;
	}

	/**
	 * @return mixed
	 */
	public function setLastVisit(?\DateTimeInterface $lastVisit): self
	{
		$this->lastVisit = $lastVisit;

		return $this;
	}

	public function getAcquisitionType(): ?string
	{
		return $this->acquisitionType;
	}

	/**
	 * @return mixed
	 */
	public function setAcquisitionType(?string $acquisitionType): self
	{
		$this->acquisitionType = $acquisitionType;

		return $this;
	}

	public function getFormSubmissions(): ?int
	{
		return $this->formSubmissions;
	}

	/**
	 * @return mixed
	 */
	public function setFormSubmissions(?int $formSubmissions): self
	{
		$this->formSubmissions = $formSubmissions;

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

	public function getSaleType(): ?string
	{
		return $this->saleType;
	}

	/**
	 * @return mixed
	 */
	public function setSaleType(?string $saleType): self
	{
		$this->saleType = $saleType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOwner(): ?InternalUser
	{
		return $this->owner;
	}

	/**
	 * @return mixed
	 */
	public function setOwner(?InternalUser $owner): self
	{
		$this->owner = $owner;

		return $this;
	}

	public function getDivision(): ?string
	{
		return $this->division;
	}

	public function setDivision(?string $division): self
	{
		$this->division = $division;

		return $this;
	}

	public function getCloseDate(): ?\DateTimeInterface
	{
		return $this->closeDate;
	}

	public function setCloseDate(?\DateTimeInterface $closeDate): self
	{
		$this->closeDate = $closeDate;

		return $this;
	}

	public function getRecentDealCloseDate(): ?\DateTimeInterface
	{
		return $this->recentDealCloseDate;
	}

	public function setRecentDealCloseDate(?\DateTimeInterface $recentDealCloseDate): self
	{
		$this->recentDealCloseDate = $recentDealCloseDate;

		return $this;
	}

	public function getCurrentAccountComStatus(): ?string
	{
		return $this->currentAccountComStatus;
	}

	public function setCurrentAccountComStatus(?string $currentAccountComStatus): self
	{
		$this->currentAccountComStatus = $currentAccountComStatus;

		return $this;
	}

	public function getDaysToClose(): ?string
	{
		return $this->daysToClose;
	}

	public function setDaysToClose(?string $daysToClose): self
	{
		$this->daysToClose = $daysToClose;

		return $this;
	}

	public function getLeadStatus(): ?string
	{
		return $this->leadStatus;
	}

	public function setLeadStatus(?string $leadStatus): self
	{
		$this->leadStatus = $leadStatus;

		return $this;
	}

	public function getLastEngagementDate(): ?string
	{
		return $this->lastEngagementDate;
	}

	public function setLastEngagementDate(?string $lastEngagementDate): self
	{
		$this->lastEngagementDate = $lastEngagementDate;

		return $this;
	}

	public function getLastContactedDate(): ?\DateTimeInterface
	{
		return $this->lastContactedDate;
	}

	public function setLastContactedDate(?\DateTimeInterface $lastContactedDate): self
	{
		$this->lastContactedDate = $lastContactedDate;

		return $this;
	}

	public function getReferralDate(): ?\DateTimeInterface
	{
		return $this->referralDate;
	}

	public function setReferralDate(?\DateTimeInterface $referralDate): self
	{
		$this->referralDate = $referralDate;

		return $this;
	}

	public function getReferralType(): ?string
	{
		return $this->referralType;
	}

	public function setReferralType(?string $referralType): self
	{
		$this->referralType = $referralType;

		return $this;
	}

	public function getReferredBy(): ?string
	{
		return $this->referredBy;
	}

	public function setReferredBy(?string $referredBy): self
	{
		$this->referredBy = $referredBy;

		return $this;
	}

	public function getResponsibleForReferral(): ?string
	{
		return $this->responsibleForReferral;
	}

	public function setResponsibleForReferral(?string $responsibleForReferral): self
	{
		$this->responsibleForReferral = $responsibleForReferral;

		return $this;
	}

	public function getHsParentCustomer(): ?self
	{
		return $this->hsParentCustomer;
	}

	public function setHsParentCustomer(?self $hsParentCustomer): self
	{
		$this->hsParentCustomer = $hsParentCustomer;

		return $this;
	}

	/**
	 * @return Collection<int, HsDeal>
	 */
	public function getHsDeals(): Collection
	{
		return $this->hsDeals;
	}

	public function addHsDeal(HsDeal $hsDeal): self
	{
		if (!$this->hsDeals->contains($hsDeal)) {
			$this->hsDeals->add($hsDeal);
			$hsDeal->setHsCustomer($this);
		}

		return $this;
	}

	public function removeHsDeal(HsDeal $hsDeal): self
	{
		if ($this->hsDeals->removeElement($hsDeal)) {
			// set the owning side to null (unless already changed)
			if ($hsDeal->getHsCustomer() === $this) {
				$hsDeal->setHsCustomer(null);
			}
		}

		return $this;
	}
}
