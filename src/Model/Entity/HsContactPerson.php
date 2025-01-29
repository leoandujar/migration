<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_contact_person')]
#[ORM\Entity]
class HsContactPerson implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_contact_person_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_contact_person_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hscontact_person_id', type: 'bigint', nullable: false)]
	private string $hsContactPerson;

	#[ORM\OneToOne(targetEntity: ContactPerson::class)]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ContactPerson $contactPerson;

	#[ORM\Column(name: 'hs_customer_id', type: 'bigint', nullable: true)]
	private ?string $hsCustomerId;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'hsContactPersons', cascade: ['persist'])]
	private InternalUser $owner;

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

	#[ORM\Column(name: 'became_customer_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameCustomerDate;

	#[ORM\Column(name: 'became_lead_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameLeadDate;

	#[ORM\Column(name: 'became_marketing_lead_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameMarketingLeadDate;

	#[ORM\Column(name: 'became_sales_lead_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameSalesLeadDate;

	#[ORM\Column(name: 'became_subscriber_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameSubscriberDate;

	#[ORM\Column(name: 'became_opportunity_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $becameOpportunityDate;

	#[ORM\Column(name: 'buying_role', type: 'string', nullable: true)]
	private ?string $buyingRole;

	#[ORM\Column(name: 'city', type: 'string', nullable: true)]
	private ?string $city;

	#[ORM\Column(name: 'state', type: 'string', nullable: true)]
	private ?string $state;

	#[ORM\Column(name: 'country', type: 'string', nullable: true)]
	private ?string $country;

	#[ORM\Column(name: 'first_name', type: 'string', nullable: true)]
	private ?string $firstName;

	#[ORM\Column(name: 'last_name', type: 'string', nullable: true)]
	private ?string $lastName;

	#[ORM\Column(name: 'industry', type: 'string', nullable: true)]
	private ?string $industry;

	#[ORM\Column(name: 'job_title', type: 'string', nullable: true)]
	private ?string $jobTitle;

	#[ORM\Column(name: 'lead_source_event', type: 'string', nullable: true)]
	private ?string $leadSourceEvent;

	#[ORM\Column(name: 'lifecicle_stage', type: 'string', nullable: true)]
	private ?string $lifecicleStage;

	#[ORM\Column(name: 'sales_activities', type: 'integer', nullable: true)]
	private ?int $salesActivities;

	#[ORM\Column(name: 'persona', type: 'string', nullable: true)]
	private ?string $persona;

	#[ORM\Column(name: 'last_sales_email_last_opened_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastSalesEmailLastOpenedDate;

	#[ORM\Column(name: 'last_sales_email_last_replied_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastSalesEmailLastRepliedDate;

	#[ORM\Column(name: 'last_sales_email_last_clicked_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastSalesEmailLastClickedDate;

	#[ORM\Column(name: 'subscriber_to_newsletter', type: 'boolean', nullable: true, options: ['default' => 'false'])]
	private ?bool $subscriberToNewsletter;

	#[ORM\Column(name: 'facebook_clicks', type: 'integer', nullable: true)]
	private ?int $facebookClicks;

	#[ORM\Column(name: 'twitter_clicks', type: 'integer', nullable: true)]
	private ?int $twitterClicks;

	#[ORM\Column(name: 'linkedin_clicks', type: 'integer', nullable: true)]
	private ?int $linkedinClicks;

	#[ORM\Column(name: 'email_first_click_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $emailFirstClickDate;

	#[ORM\Column(name: 'email_first_open_date', type: 'datetime', nullable: true)]
	private  ?\DateTimeInterface $emailFirstOpenDate;

	#[ORM\Column(name: 'email_first_send_date', type: 'datetime', nullable: true)]
	private  ?\DateTimeInterface $emailFirstSendDate;

	#[ORM\Column(name: 'email_last_click_date', type: 'datetime', nullable: true)]
	private  ?\DateTimeInterface $emailLastClickDate;

	#[ORM\Column(name: 'email_last_open_date', type: 'datetime', nullable: true)]
	private  ?\DateTimeInterface $emailLastOpenDate;

	#[ORM\Column(name: 'email_last_send_date', type: 'datetime', nullable: true)]
	private  ?\DateTimeInterface $emailLastSendDate;

	#[ORM\Column(name: 'email_clicks', type: 'integer', nullable: true)]
	private ?int $emailClicks;

	#[ORM\Column(name: 'email_delivered', type: 'integer', nullable: true)]
	private ?int $emailDelivered;

	#[ORM\Column(name: 'email_opened', type: 'integer', nullable: true)]
	private ?int $emailOpened;

	#[ORM\Column(name: 'email_sends_since_last_engagement', type: 'integer', nullable: true)]
	private ?int $emailSendsSinceLastEngagement;

	#[ORM\Column(name: 'fist_referrer_site', type: 'text', nullable: true)]
	private ?string $firstReferrerSite;

	#[ORM\Column(name: 'first_url', type: 'text', nullable: true)]
	private ?string $firstUrl;

	#[ORM\Column(name: 'visits', type: 'integer', nullable: true)]
	private ?int $visits;

	#[ORM\Column(name: 'source', type: 'string', nullable: true)]
	private ?string $source;

	#[ORM\Column(name: 'first_conversion', type: 'string', nullable: true)]
	private ?string $firstConversion;

	#[ORM\Column(name: 'lead_source', type: 'string', nullable: true)]
	private ?string $leadSource;

	#[ORM\Column(name: 'referrer_first_name', type: 'string', nullable: true)]
	private ?string $referrerFirstName;

	#[ORM\Column(name: 'referrer_last_name', type: 'string', nullable: true)]
	private ?string $referrerLastName;

	#[ORM\Column(name: 'division', type: 'string', nullable: true)]
	private ?string $division;

	#[ORM\Column(name: 'lifecyclestage_other_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lifecyclestageOtherDate;

	#[ORM\Column(name: 'child_company', type: 'string', nullable: true)]
	private string $childCompany;

	#[ORM\Column(name: 'company', type: 'string', nullable: true)]
	private string $company;

	#[ORM\Column(name: 'last_contacted_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastContactedDate;

	#[ORM\Column(name: 'last_engagement_date', type: 'string', nullable: true)]
	private ?string $lastEngagementDate;

	#[ORM\Column(name: 'lead_status', type: 'string', nullable: true)]
	private ?string $leadStatus;

	#[ORM\Column(name: 'mql_score', type: 'string', nullable: true)]
	private ?string $mqlScore;

	#[ORM\Column(name: 'num_sequences_enrolled', type: 'string', nullable: true)]
	private ?string $numSequencesEnrolled;

	#[ORM\Column(name: 'num_times_contacted', type: 'string', nullable: true)]
	private ?string $numTimesContacted;

	#[ORM\Column(name: 'reference', type: 'string', nullable: true)]
	private ?string $reference;

	#[ORM\Column(name: 'willing_to_be_a_reference', type: 'string', nullable: true)]
	private ?string $willingToBeAReference;

	#[ORM\Column(name: 'num_form_submissions', type: 'string', nullable: true)]
	private ?string $numFormSubmissions;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getHsContactPerson(): ?string
	{
		return $this->hsContactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setHsContactPerson(string $hsContactPerson): self
	{
		$this->hsContactPerson = $hsContactPerson;

		return $this;
	}

	public function getHsCustomerId(): ?string
	{
		return $this->hsCustomerId;
	}

	/**
	 * @return mixed
	 */
	public function setHsCustomerId(?string $hsCustomerId): self
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

	public function getBecameCustomerDate(): ?\DateTimeInterface
	{
		return $this->becameCustomerDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameCustomerDate(?\DateTimeInterface $becameCustomerDate): self
	{
		$this->becameCustomerDate = $becameCustomerDate;

		return $this;
	}

	public function getBecameLeadDate(): ?\DateTimeInterface
	{
		return $this->becameLeadDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameLeadDate(?\DateTimeInterface $becameLeadDate): self
	{
		$this->becameLeadDate = $becameLeadDate;

		return $this;
	}

	public function getBecameMarketingLeadDate(): ?\DateTimeInterface
	{
		return $this->becameMarketingLeadDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameMarketingLeadDate(?\DateTimeInterface $becameMarketingLeadDate): self
	{
		$this->becameMarketingLeadDate = $becameMarketingLeadDate;

		return $this;
	}

	public function getBecameSalesLeadDate(): ?\DateTimeInterface
	{
		return $this->becameSalesLeadDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameSalesLeadDate(?\DateTimeInterface $becameSalesLeadDate): self
	{
		$this->becameSalesLeadDate = $becameSalesLeadDate;

		return $this;
	}

	public function getBecameSubscriberDate(): ?\DateTimeInterface
	{
		return $this->becameSubscriberDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameSubscriberDate(?\DateTimeInterface $becameSubscriberDate): self
	{
		$this->becameSubscriberDate = $becameSubscriberDate;

		return $this;
	}

	public function getBecameOpportunityDate(): ?\DateTimeInterface
	{
		return $this->becameOpportunityDate;
	}

	/**
	 * @return mixed
	 */
	public function setBecameOpportunityDate(?\DateTimeInterface $becameOpportunityDate): self
	{
		$this->becameOpportunityDate = $becameOpportunityDate;

		return $this;
	}

	public function getBuyingRole(): ?string
	{
		return $this->buyingRole;
	}

	/**
	 * @return mixed
	 */
	public function setBuyingRole(?string $buyingRole): self
	{
		$this->buyingRole = $buyingRole;

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

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	/**
	 * @return mixed
	 */
	public function setFirstName(?string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	/**
	 * @return mixed
	 */
	public function setLastName(?string $lastName): self
	{
		$this->lastName = $lastName;

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

	public function getJobTitle(): ?string
	{
		return $this->jobTitle;
	}

	/**
	 * @return mixed
	 */
	public function setJobTitle(?string $jobTitle): self
	{
		$this->jobTitle = $jobTitle;

		return $this;
	}

	public function getLeadSourceEvent(): ?string
	{
		return $this->leadSourceEvent;
	}

	/**
	 * @return mixed
	 */
	public function setLeadSourceEvent(?string $leadSourceEvent): self
	{
		$this->leadSourceEvent = $leadSourceEvent;

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

	public function getSalesActivities(): ?int
	{
		return $this->salesActivities;
	}

	/**
	 * @return mixed
	 */
	public function setSalesActivities(?int $salesActivities): self
	{
		$this->salesActivities = $salesActivities;

		return $this;
	}

	public function getPersona(): ?string
	{
		return $this->persona;
	}

	/**
	 * @return mixed
	 */
	public function setPersona(?string $persona): self
	{
		$this->persona = $persona;

		return $this;
	}

	public function getLastSalesEmailLastOpenedDate(): ?\DateTimeInterface
	{
		return $this->lastSalesEmailLastOpenedDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastSalesEmailLastOpenedDate(?\DateTimeInterface $lastSalesEmailLastOpenedDate): self
	{
		$this->lastSalesEmailLastOpenedDate = $lastSalesEmailLastOpenedDate;

		return $this;
	}

	public function getLastSalesEmailLastRepliedDate(): ?\DateTimeInterface
	{
		return $this->lastSalesEmailLastRepliedDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastSalesEmailLastRepliedDate(?\DateTimeInterface $lastSalesEmailLastRepliedDate): self
	{
		$this->lastSalesEmailLastRepliedDate = $lastSalesEmailLastRepliedDate;

		return $this;
	}

	public function getLastSalesEmailLastClickedDate(): ?\DateTimeInterface
	{
		return $this->lastSalesEmailLastClickedDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastSalesEmailLastClickedDate(?\DateTimeInterface $lastSalesEmailLastClickedDate): self
	{
		$this->lastSalesEmailLastClickedDate = $lastSalesEmailLastClickedDate;

		return $this;
	}

	public function getSubscriberToNewsletter(): ?bool
	{
		return $this->subscriberToNewsletter;
	}

	/**
	 * @return mixed
	 */
	public function setSubscriberToNewsletter(?bool $subscriberToNewsletter): self
	{
		$this->subscriberToNewsletter = $subscriberToNewsletter;

		return $this;
	}

	public function getFacebookClicks(): ?int
	{
		return $this->facebookClicks;
	}

	/**
	 * @return mixed
	 */
	public function setFacebookClicks(?int $facebookClicks): self
	{
		$this->facebookClicks = $facebookClicks;

		return $this;
	}

	public function getTwitterClicks(): ?int
	{
		return $this->twitterClicks;
	}

	/**
	 * @return mixed
	 */
	public function setTwitterClicks(?int $twitterClicks): self
	{
		$this->twitterClicks = $twitterClicks;

		return $this;
	}

	public function getLinkedinClicks(): ?int
	{
		return $this->linkedinClicks;
	}

	/**
	 * @return mixed
	 */
	public function setLinkedinClicks(?int $linkedinClicks): self
	{
		$this->linkedinClicks = $linkedinClicks;

		return $this;
	}

	public function getEmailFirstClickDate(): ?\DateTimeInterface
	{
		return $this->emailFirstClickDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailFirstClickDate(?\DateTimeInterface $emailFirstClickDate): self
	{
		$this->emailFirstClickDate = $emailFirstClickDate;

		return $this;
	}

	public function getEmailFirstOpenDate(): ?\DateTimeInterface
	{
		return $this->emailFirstOpenDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailFirstOpenDate(?\DateTimeInterface $emailFirstOpenDate): self
	{
		$this->emailFirstOpenDate = $emailFirstOpenDate;

		return $this;
	}

	public function getEmailFirstSendDate(): ?\DateTimeInterface
	{
		return $this->emailFirstSendDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailFirstSendDate(?\DateTimeInterface $emailFirstSendDate): self
	{
		$this->emailFirstSendDate = $emailFirstSendDate;

		return $this;
	}

	public function getEmailLastClickDate(): ?\DateTimeInterface
	{
		return $this->emailLastClickDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailLastClickDate(?\DateTimeInterface $emailLastClickDate): self
	{
		$this->emailLastClickDate = $emailLastClickDate;

		return $this;
	}

	public function getEmailLastOpenDate(): ?\DateTimeInterface
	{
		return $this->emailLastOpenDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailLastOpenDate(?\DateTimeInterface $emailLastOpenDate): self
	{
		$this->emailLastOpenDate = $emailLastOpenDate;

		return $this;
	}

	public function getEmailLastSendDate(): ?\DateTimeInterface
	{
		return $this->emailLastSendDate;
	}

	/**
	 * @return mixed
	 */
	public function setEmailLastSendDate(?\DateTimeInterface $emailLastSendDate): self
	{
		$this->emailLastSendDate = $emailLastSendDate;

		return $this;
	}

	public function getEmailClicks(): ?int
	{
		return $this->emailClicks;
	}

	/**
	 * @return mixed
	 */
	public function setEmailClicks(?int $emailClicks): self
	{
		$this->emailClicks = $emailClicks;

		return $this;
	}

	public function getEmailDelivered(): ?int
	{
		return $this->emailDelivered;
	}

	/**
	 * @return mixed
	 */
	public function setEmailDelivered(?int $emailDelivered): self
	{
		$this->emailDelivered = $emailDelivered;

		return $this;
	}

	public function getEmailOpened(): ?int
	{
		return $this->emailOpened;
	}

	/**
	 * @return mixed
	 */
	public function setEmailOpened(?int $emailOpened): self
	{
		$this->emailOpened = $emailOpened;

		return $this;
	}

	public function getEmailSendsSinceLastEngagement(): ?int
	{
		return $this->emailSendsSinceLastEngagement;
	}

	/**
	 * @return mixed
	 */
	public function setEmailSendsSinceLastEngagement(?int $emailSendsSinceLastEngagement): self
	{
		$this->emailSendsSinceLastEngagement = $emailSendsSinceLastEngagement;

		return $this;
	}

	public function getFirstReferrerSite(): ?string
	{
		return $this->firstReferrerSite;
	}

	/**
	 * @return mixed
	 */
	public function setFirstReferrerSite(?string $firstReferrerSite): self
	{
		$this->firstReferrerSite = $firstReferrerSite;

		return $this;
	}

	public function getFirstUrl(): ?string
	{
		return $this->firstUrl;
	}

	/**
	 * @return mixed
	 */
	public function setFirstUrl(?string $firstUrl): self
	{
		$this->firstUrl = $firstUrl;

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

	public function getSource(): ?string
	{
		return $this->source;
	}

	/**
	 * @return mixed
	 */
	public function setSource(?string $source): self
	{
		$this->source = $source;

		return $this;
	}

	public function getFirstConversion(): ?string
	{
		return $this->firstConversion;
	}

	/**
	 * @return mixed
	 */
	public function setFirstConversion(?string $firstConversion): self
	{
		$this->firstConversion = $firstConversion;

		return $this;
	}

	public function getLeadSource(): ?string
	{
		return $this->leadSource;
	}

	/**
	 * @return mixed
	 */
	public function setLeadSource(?string $leadSource): self
	{
		$this->leadSource = $leadSource;

		return $this;
	}

	public function getReferrerFirstName(): ?string
	{
		return $this->referrerFirstName;
	}

	/**
	 * @return mixed
	 */
	public function setReferrerFirstName(?string $referrerFirstName): self
	{
		$this->referrerFirstName = $referrerFirstName;

		return $this;
	}

	public function getReferrerLastName(): ?string
	{
		return $this->referrerLastName;
	}

	/**
	 * @return mixed
	 */
	public function setReferrerLastName(?string $referrerLastName): self
	{
		$this->referrerLastName = $referrerLastName;

		return $this;
	}

	public function getContactPerson(): ?ContactPerson
	{
		return $this->contactPerson;
	}

	/**
	 * @return mixed
	 */
	public function setContactPerson(?ContactPerson $contactPerson): self
	{
		$this->contactPerson = $contactPerson;

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

	public function isSubscriberToNewsletter(): ?bool
	{
		return $this->subscriberToNewsletter;
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

	public function getChildCompany(): ?string
	{
		return $this->childCompany;
	}

	public function setChildCompany(?string $childCompany): self
	{
		$this->childCompany = $childCompany;

		return $this;
	}

	public function getCompany(): ?string
	{
		return $this->company;
	}

	public function setCompany(?string $company): self
	{
		$this->company = $company;

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

	public function getLeadStatus(): ?string
	{
		return $this->leadStatus;
	}

	public function setLeadStatus(?string $leadStatus): self
	{
		$this->leadStatus = $leadStatus;

		return $this;
	}

	public function getMqlScore(): ?string
	{
		return $this->mqlScore;
	}

	public function setMqlScore(?string $mqlScore): self
	{
		$this->mqlScore = $mqlScore;

		return $this;
	}

	public function getNumSequencesEnrolled(): ?string
	{
		return $this->numSequencesEnrolled;
	}

	public function setNumSequencesEnrolled(?string $numSequencesEnrolled): self
	{
		$this->numSequencesEnrolled = $numSequencesEnrolled;

		return $this;
	}

	public function getNumTimesContacted(): ?string
	{
		return $this->numTimesContacted;
	}

	public function setNumTimesContacted(?string $numTimesContacted): self
	{
		$this->numTimesContacted = $numTimesContacted;

		return $this;
	}

	public function getReference(): ?string
	{
		return $this->reference;
	}

	public function setReference(?string $reference): self
	{
		$this->reference = $reference;

		return $this;
	}

	public function getWillingToBeAReference(): ?string
	{
		return $this->willingToBeAReference;
	}

	public function setWillingToBeAReference(?string $willingToBeAReference): self
	{
		$this->willingToBeAReference = $willingToBeAReference;

		return $this;
	}

	public function getNumFormSubmissions(): ?string
	{
		return $this->numFormSubmissions;
	}

	public function setNumFormSubmissions(?string $numFormSubmissions): self
	{
		$this->numFormSubmissions = $numFormSubmissions;

		return $this;
	}

	public function getLifecyclestageOtherDate(): ?\DateTimeInterface
	{
		return $this->lifecyclestageOtherDate;
	}

	public function setLifecyclestageOtherDate(?\DateTimeInterface $lifecyclestageOtherDate): self
	{
		$this->lifecyclestageOtherDate = $lifecyclestageOtherDate;

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
}
