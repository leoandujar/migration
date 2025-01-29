<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_deal')]
#[ORM\Entity]
class HsDeal implements EntityInterface
{
	public const ASSOCIATION_TYPE_COMPANY = 'deal_to_company';
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_deal_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_deal_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hsdeal_id', type: 'bigint', nullable: false)]
	private string $hsDealId;

	#[ORM\ManyToOne(targetEntity: HsCustomer::class, cascade: ['persist'], inversedBy: 'hsDeals')]
	#[ORM\JoinColumn(name: 'hs_customer_id', referencedColumnName: 'hs_customer_id', nullable: true)]
	private ?HsCustomer $hsCustomer;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'hsDeals', cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $owner;

	#[ORM\Column(name: 'amount', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $amount;

	#[ORM\Column(name: 'closed_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $closedDate;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'stage', type: 'string', nullable: false)]
	private string $stage;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'estimated_rfp_amount', type: 'string', nullable: true)]
	private ?string $estimatedRfpAmount;

	#[ORM\Column(name: 'industry', type: 'string', nullable: true)]
	private ?string $industry;

	#[ORM\Column(name: 'last_activity_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastActivityDate;

	#[ORM\Column(name: 'sales_activities', type: 'integer', nullable: true)]
	private ?int $salesActivities;

	#[ORM\Column(name: 'pipeline_id', type: 'string', nullable: true)]
	private ?string $pipelineId;

	#[ORM\Column(name: 'reason_deal_lost', type: 'string', nullable: true)]
	private ?string $reasonDealLost;

	#[ORM\Column(name: 'days_to_close', type: 'integer', nullable: true)]
	private int $daysToClose;

	#[ORM\Column(name: 'hs_closed_amount', type: 'string', nullable: true)]
	private ?string $hsClosedAmount;

	#[ORM\Column(name: 'hs_deal_stage_probability', type: 'string', nullable: true)]
	private ?string $hsDealStageProbability;

	#[ORM\Column(name: 'hs_forecast_amount', type: 'string', nullable: true)]
	private ?string $hsForecastAmount;

	#[ORM\Column(name: 'hs_is_closed', type: 'string', nullable: true)]
	private ?string $hsIsClosed;

	#[ORM\Column(name: 'hs_is_closed_won', type: 'string', nullable: true)]
	private ?string $hsIsClosedWon;

	#[ORM\Column(name: 'hs_latest_meeting_activity', type: 'string', nullable: true)]
	private ?string $hsLatestMeetingActivity;

	#[ORM\Column(name: 'hs_num_target_accounts', type: 'string', nullable: true)]
	private ?string $hsNumTargetAccounts;

	#[ORM\Column(name: 'hs_projected_amount', type: 'string', nullable: true)]
	private ?string $hsProjectedAmount;

	#[ORM\Column(name: 'hs_sales_email_last_replied', type: 'string', nullable: true)]
	private ?string $hsSalesEmailLastReplied;

	#[ORM\Column(name: 'notes_last_contacted', type: 'string', nullable: true)]
	private ?string $notesLastContacted;

	#[ORM\Column(name: 'num_associated_contacts', type: 'string', nullable: true)]
	private ?string $numAssociatedContacts;

	#[ORM\Column(name: 'num_notes', type: 'string', nullable: true)]
	private ?string $numNotes;

	#[ORM\Column(name: 'services_requested', type: 'string', nullable: true)]
	private ?string $servicesRequested;

	#[ORM\Column(name: 'copies_of_all_bids_received', type: 'string', nullable: true)]
	private ?string $copiesOfAllBidsReceived;

	#[ORM\Column(name: 'go_no_go_score', type: 'string', nullable: true)]
	private ?string $goNoGoScore;

	#[ORM\Column(name: 'hs_acv', type: 'string', nullable: true)]
	private ?string $hsAcv;

	#[ORM\Column(name: 'hs_analytics_source', type: 'string', nullable: true)]
	private ?string $hsAnalyticsSource;

	#[ORM\Column(name: 'hs_analytics_source_data_1', type: 'string', nullable: true)]
	private ?string $hsAnalyticsSourceData1;

	#[ORM\Column(name: 'hs_analytics_source_data_2', type: 'string', nullable: true)]
	private ?string $hsAnalyticsSourceData2;

	#[ORM\Column(name: 'hs_campaign', type: 'string', nullable: true)]
	private ?string $hsCampaign;

	#[ORM\Column(name: 'hs_deal_amount_calculation_preference', type: 'string', nullable: true)]
	private ?string $hsDealAmountCalculationPreference;

	#[ORM\Column(name: 'hs_deal_stage_probability_shadow', type: 'string', nullable: true)]
	private ?string $hsDealStageProbabilityShadow;

	#[ORM\Column(name: 'hs_forecast_probability', type: 'string', nullable: true)]
	private ?string $hsForecastProbability;

	#[ORM\Column(name: 'hs_likelihood_to_close', type: 'string', nullable: true)]
	private ?string $hsLikelihoodToClose;

	#[ORM\Column(name: 'hs_manual_forecast_category', type: 'string', nullable: true)]
	private ?string $hsManualForecastCategory;

	#[ORM\Column(name: 'hs_mrr', type: 'string', nullable: true)]
	private ?string $hsMrr;

	#[ORM\Column(name: 'hs_next_step', type: 'string', nullable: true)]
	private ?string $hsNextStep;

	#[ORM\Column(name: 'hs_num_associated_deal_splits', type: 'string', nullable: true)]
	private ?string $hsNumAssociatedDealSplits;

	#[ORM\Column(name: 'hs_predicted_amount', type: 'string', nullable: true)]
	private ?string $hsPredictedAmount;

	#[ORM\Column(name: 'hs_priority', type: 'string', nullable: true)]
	private ?string $hsPriority;

	#[ORM\Column(name: 'hs_tcv', type: 'string', nullable: true)]
	private ?string $hsTcv;

	#[ORM\Column(name: 'reason_for_no_bid', type: 'string', nullable: true)]
	private ?string $reasonForNoBid;

	#[ORM\Column(name: 'successful_bidder', type: 'string', nullable: true)]
	private ?string $successfulBidder;

	#[ORM\Column(name: 'engagements_last_meeting_booked', type: 'string', nullable: true)]
	private ?string $engagementsLastMeetingBooked;

	#[ORM\Column(name: 'engagements_last_meeting_booked_medium', type: 'string', nullable: true)]
	private ?string $engagementsLastMeetingBookedMedium;

	#[ORM\Column(name: 'id_quotes', type: 'string', nullable: true)]
	private ?string $idQuotes;

	#[ORM\Column(name: 'closed_won_reason', type: 'string', nullable: true)]
	private ?string $closedWonReason;

	#[ORM\Column(name: 'annual_contract_amount', type: 'string', nullable: true)]
	private ?string $annualContractAmount;

	#[ORM\Column(name: 'excited_for_this_bid', type: 'string', nullable: true)]
	private ?string $excitedforThisBid;

	#[ORM\Column(name: 'contract_term_ending_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $contractTermEndingDate;

	#[ORM\Column(name: 'go_hs_generated', type: 'string', nullable: true)]
	private ?string $goHsGenerated;

	#[ORM\Column(name: 'go_score', type: 'string', nullable: true)]
	private ?string $goScore;

	#[ORM\Column(name: 'initial_caller_client_services_member', type: 'string', nullable: true)]
	private ?string $initialCallerClientServicesMember;

	#[ORM\Column(name: 'num_times_contacted', type: 'string', nullable: true)]
	private ?string $numTimesContacted;

	#[ORM\Column(name: 'opportunity_ratio', type: 'string', nullable: true)]
	private ?string $opportunityRatio;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getHsDealId(): ?string
	{
		return $this->hsDealId;
	}

	/**
	 * @return mixed
	 */
	public function setHsDealId(string $hsDealId): self
	{
		$this->hsDealId = $hsDealId;

		return $this;
	}

	public function getAmount(): ?string
	{
		return $this->amount;
	}

	/**
	 * @return mixed
	 */
	public function setAmount(?string $amount): self
	{
		$this->amount = $amount;

		return $this;
	}

	public function getClosedDate(): ?\DateTimeInterface
	{
		return $this->closedDate;
	}

	/**
	 * @return mixed
	 */
	public function setClosedDate(?\DateTimeInterface $closedDate): self
	{
		$this->closedDate = $closedDate;

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

	public function getStage(): ?string
	{
		return $this->stage;
	}

	/**
	 * @return mixed
	 */
	public function setStage(string $stage): self
	{
		$this->stage = $stage;

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

	public function getEstimatedRfpAmount(): ?string
	{
		return $this->estimatedRfpAmount;
	}

	/**
	 * @return mixed
	 */
	public function setEstimatedRfpAmount(?string $estimatedRfpAmount): self
	{
		$this->estimatedRfpAmount = $estimatedRfpAmount;

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

	public function getPipelineId(): ?string
	{
		return $this->pipelineId;
	}

	/**
	 * @return mixed
	 */
	public function setPipelineId(?string $pipelineId): self
	{
		$this->pipelineId = $pipelineId;

		return $this;
	}

	public function getReasonDealLost(): ?string
	{
		return $this->reasonDealLost;
	}

	/**
	 * @return mixed
	 */
	public function setReasonDealLost(?string $reasonDealLost): self
	{
		$this->reasonDealLost = $reasonDealLost;

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

	public function getDaysToClose(): ?int
	{
		return $this->daysToClose;
	}

	public function setDaysToClose(?int $daysToClose): self
	{
		$this->daysToClose = $daysToClose;

		return $this;
	}

	public function getHsClosedAmount(): ?string
	{
		return $this->hsClosedAmount;
	}

	public function setHsClosedAmount(?string $hsClosedAmount): self
	{
		$this->hsClosedAmount = $hsClosedAmount;

		return $this;
	}

	public function getHsDealStageProbability(): ?string
	{
		return $this->hsDealStageProbability;
	}

	public function setHsDealStageProbability(?string $hsDealStageProbability): self
	{
		$this->hsDealStageProbability = $hsDealStageProbability;

		return $this;
	}

	public function getHsForecastAmount(): ?string
	{
		return $this->hsForecastAmount;
	}

	public function setHsForecastAmount(?string $hsForecastAmount): self
	{
		$this->hsForecastAmount = $hsForecastAmount;

		return $this;
	}

	public function getHsIsClosed(): ?string
	{
		return $this->hsIsClosed;
	}

	public function setHsIsClosed(?string $hsIsClosed): self
	{
		$this->hsIsClosed = $hsIsClosed;

		return $this;
	}

	public function getHsIsClosedWon(): ?string
	{
		return $this->hsIsClosedWon;
	}

	public function setHsIsClosedWon(?string $hsIsClosedWon): self
	{
		$this->hsIsClosedWon = $hsIsClosedWon;

		return $this;
	}

	public function getHsLatestMeetingActivity(): ?string
	{
		return $this->hsLatestMeetingActivity;
	}

	public function setHsLatestMeetingActivity(?string $hsLatestMeetingActivity): self
	{
		$this->hsLatestMeetingActivity = $hsLatestMeetingActivity;

		return $this;
	}

	public function getHsNumTargetAccounts(): ?string
	{
		return $this->hsNumTargetAccounts;
	}

	public function setHsNumTargetAccounts(?string $hsNumTargetAccounts): self
	{
		$this->hsNumTargetAccounts = $hsNumTargetAccounts;

		return $this;
	}

	public function getHsProjectedAmount(): ?string
	{
		return $this->hsProjectedAmount;
	}

	public function setHsProjectedAmount(?string $hsProjectedAmount): self
	{
		$this->hsProjectedAmount = $hsProjectedAmount;

		return $this;
	}

	public function getHsSalesEmailLastReplied(): ?string
	{
		return $this->hsSalesEmailLastReplied;
	}

	public function setHsSalesEmailLastReplied(?string $hsSalesEmailLastReplied): self
	{
		$this->hsSalesEmailLastReplied = $hsSalesEmailLastReplied;

		return $this;
	}

	public function getNotesLastContacted(): ?string
	{
		return $this->notesLastContacted;
	}

	public function setNotesLastContacted(?string $notesLastContacted): self
	{
		$this->notesLastContacted = $notesLastContacted;

		return $this;
	}

	public function getNumAssociatedContacts(): ?string
	{
		return $this->numAssociatedContacts;
	}

	public function setNumAssociatedContacts(?string $numAssociatedContacts): self
	{
		$this->numAssociatedContacts = $numAssociatedContacts;

		return $this;
	}

	public function getNumNotes(): ?string
	{
		return $this->numNotes;
	}

	public function setNumNotes(?string $numNotes): self
	{
		$this->numNotes = $numNotes;

		return $this;
	}

	public function getServicesRequested(): ?string
	{
		return $this->servicesRequested;
	}

	public function setServicesRequested(?string $servicesRequested): self
	{
		$this->servicesRequested = $servicesRequested;

		return $this;
	}

	public function getCopiesOfAllBidsReceived(): ?string
	{
		return $this->copiesOfAllBidsReceived;
	}

	public function setCopiesOfAllBidsReceived(?string $copiesOfAllBidsReceived): self
	{
		$this->copiesOfAllBidsReceived = $copiesOfAllBidsReceived;

		return $this;
	}

	public function getGoNoGoScore(): ?string
	{
		return $this->goNoGoScore;
	}

	public function setGoNoGoScore(?string $goNoGoScore): self
	{
		$this->goNoGoScore = $goNoGoScore;

		return $this;
	}

	public function getHsAcv(): ?string
	{
		return $this->hsAcv;
	}

	public function setHsAcv(?string $hsAcv): self
	{
		$this->hsAcv = $hsAcv;

		return $this;
	}

	public function getHsAnalyticsSource(): ?string
	{
		return $this->hsAnalyticsSource;
	}

	public function setHsAnalyticsSource(?string $hsAnalyticsSource): self
	{
		$this->hsAnalyticsSource = $hsAnalyticsSource;

		return $this;
	}

	public function getHsAnalyticsSourceData1(): ?string
	{
		return $this->hsAnalyticsSourceData1;
	}

	public function setHsAnalyticsSourceData1(?string $hsAnalyticsSourceData1): self
	{
		$this->hsAnalyticsSourceData1 = $hsAnalyticsSourceData1;

		return $this;
	}

	public function getHsAnalyticsSourceData2(): ?string
	{
		return $this->hsAnalyticsSourceData2;
	}

	public function setHsAnalyticsSourceData2(?string $hsAnalyticsSourceData2): self
	{
		$this->hsAnalyticsSourceData2 = $hsAnalyticsSourceData2;

		return $this;
	}

	public function getHsCampaign(): ?string
	{
		return $this->hsCampaign;
	}

	public function setHsCampaign(?string $hsCampaign): self
	{
		$this->hsCampaign = $hsCampaign;

		return $this;
	}

	public function getHsDealAmountCalculationPreference(): ?string
	{
		return $this->hsDealAmountCalculationPreference;
	}

	public function setHsDealAmountCalculationPreference(?string $hsDealAmountCalculationPreference): self
	{
		$this->hsDealAmountCalculationPreference = $hsDealAmountCalculationPreference;

		return $this;
	}

	public function getHsDealStageProbabilityShadow(): ?string
	{
		return $this->hsDealStageProbabilityShadow;
	}

	public function setHsDealStageProbabilityShadow(?string $hsDealStageProbabilityShadow): self
	{
		$this->hsDealStageProbabilityShadow = $hsDealStageProbabilityShadow;

		return $this;
	}

	public function getHsForecastProbability(): ?string
	{
		return $this->hsForecastProbability;
	}

	public function setHsForecastProbability(?string $hsForecastProbability): self
	{
		$this->hsForecastProbability = $hsForecastProbability;

		return $this;
	}

	public function getHsLikelihoodToClose(): ?string
	{
		return $this->hsLikelihoodToClose;
	}

	public function setHsLikelihoodToClose(?string $hsLikelihoodToClose): self
	{
		$this->hsLikelihoodToClose = $hsLikelihoodToClose;

		return $this;
	}

	public function getHsManualForecastCategory(): ?string
	{
		return $this->hsManualForecastCategory;
	}

	public function setHsManualForecastCategory(?string $hsManualForecastCategory): self
	{
		$this->hsManualForecastCategory = $hsManualForecastCategory;

		return $this;
	}

	public function getHsMrr(): ?string
	{
		return $this->hsMrr;
	}

	public function setHsMrr(?string $hsMrr): self
	{
		$this->hsMrr = $hsMrr;

		return $this;
	}

	public function getHsNextStep(): ?string
	{
		return $this->hsNextStep;
	}

	public function setHsNextStep(?string $hsNextStep): self
	{
		$this->hsNextStep = $hsNextStep;

		return $this;
	}

	public function getHsNumAssociatedDealSplits(): ?string
	{
		return $this->hsNumAssociatedDealSplits;
	}

	public function setHsNumAssociatedDealSplits(?string $hsNumAssociatedDealSplits): self
	{
		$this->hsNumAssociatedDealSplits = $hsNumAssociatedDealSplits;

		return $this;
	}

	public function getHsPredictedAmount(): ?string
	{
		return $this->hsPredictedAmount;
	}

	public function setHsPredictedAmount(?string $hsPredictedAmount): self
	{
		$this->hsPredictedAmount = $hsPredictedAmount;

		return $this;
	}

	public function getHsPriority(): ?string
	{
		return $this->hsPriority;
	}

	public function setHsPriority(?string $hsPriority): self
	{
		$this->hsPriority = $hsPriority;

		return $this;
	}

	public function getHsTcv(): ?string
	{
		return $this->hsTcv;
	}

	public function setHsTcv(?string $hsTcv): self
	{
		$this->hsTcv = $hsTcv;

		return $this;
	}

	public function getReasonForNoBid(): ?string
	{
		return $this->reasonForNoBid;
	}

	public function setReasonForNoBid(?string $reasonForNoBid): self
	{
		$this->reasonForNoBid = $reasonForNoBid;

		return $this;
	}

	public function getSuccessfulBidder(): ?string
	{
		return $this->successfulBidder;
	}

	public function setSuccessfulBidder(?string $successfulBidder): self
	{
		$this->successfulBidder = $successfulBidder;

		return $this;
	}

	public function getEngagementsLastMeetingBooked(): ?string
	{
		return $this->engagementsLastMeetingBooked;
	}

	public function setEngagementsLastMeetingBooked(?string $engagementsLastMeetingBooked): self
	{
		$this->engagementsLastMeetingBooked = $engagementsLastMeetingBooked;

		return $this;
	}

	public function getEngagementsLastMeetingBookedMedium(): ?string
	{
		return $this->engagementsLastMeetingBookedMedium;
	}

	public function setEngagementsLastMeetingBookedMedium(?string $engagementsLastMeetingBookedMedium): self
	{
		$this->engagementsLastMeetingBookedMedium = $engagementsLastMeetingBookedMedium;

		return $this;
	}

	public function getIdQuotes(): ?string
	{
		return $this->idQuotes;
	}

	public function setIdQuotes(?string $idQuotes): self
	{
		$this->idQuotes = $idQuotes;

		return $this;
	}

	public function getClosedWonReason(): ?string
	{
		return $this->closedWonReason;
	}

	public function setClosedWonReason(?string $closedWonReason): self
	{
		$this->closedWonReason = $closedWonReason;

		return $this;
	}

	public function getAnnualContractAmount(): ?string
	{
		return $this->annualContractAmount;
	}

	public function setAnnualContractAmount(?string $annualContractAmount): self
	{
		$this->annualContractAmount = $annualContractAmount;

		return $this;
	}

	public function getExcitedforThisBid(): ?string
	{
		return $this->excitedforThisBid;
	}

	public function setExcitedforThisBid(?string $excitedforThisBid): self
	{
		$this->excitedforThisBid = $excitedforThisBid;

		return $this;
	}

	public function getGoHsGenerated(): ?string
	{
		return $this->goHsGenerated;
	}

	public function setGoHsGenerated(?string $goHsGenerated): self
	{
		$this->goHsGenerated = $goHsGenerated;

		return $this;
	}

	public function getGoScore(): ?string
	{
		return $this->goScore;
	}

	public function setGoScore(?string $goScore): self
	{
		$this->goScore = $goScore;

		return $this;
	}

	public function getInitialCallerClientServicesMember(): ?string
	{
		return $this->initialCallerClientServicesMember;
	}

	public function setInitialCallerClientServicesMember(?string $initialCallerClientServicesMember): self
	{
		$this->initialCallerClientServicesMember = $initialCallerClientServicesMember;

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

	public function getOpportunityRatio(): ?string
	{
		return $this->opportunityRatio;
	}

	public function setOpportunityRatio(?string $opportunityRatio): self
	{
		$this->opportunityRatio = $opportunityRatio;

		return $this;
	}

	public function getContractTermEndingDate(): ?\DateTimeInterface
	{
		return $this->contractTermEndingDate;
	}

	public function setContractTermEndingDate(?\DateTimeInterface $contractTermEndingDate): self
	{
		$this->contractTermEndingDate = $contractTermEndingDate;

		return $this;
	}

	public function getHsCustomer(): ?HsCustomer
	{
		return $this->hsCustomer;
	}

	public function setHsCustomer(?HsCustomer $hsCustomer): self
	{
		$this->hsCustomer = $hsCustomer;

		return $this;
	}
}
