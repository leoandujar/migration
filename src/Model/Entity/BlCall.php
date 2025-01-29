<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_call')]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\BlCallRepository')]
class BlCall implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_call_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_call_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blcall_id', type: 'bigint', nullable: false)]
	private string $blCallId;

	#[ORM\Column(name: 'bl_reference_id', type: 'bigint', nullable: false)]
	private string $blReferenceId;

	#[ORM\Column(name: 'is_crowd_client', type: 'boolean', nullable: false)]
	private bool $isCrowdClient;

	#[ORM\Column(name: 'interpreter_name', type: 'string', nullable: true)]
	private ?string $interpreterName;

	#[ORM\Column(name: 'interpreter_referral_number', type: 'string', nullable: false)]
	private string $interpreterReferralNumber;

	#[ORM\ManyToOne(targetEntity: BlContact::class)]
	#[ORM\JoinColumn(name: 'bl_contact_id', referencedColumnName: 'bl_contact_id', nullable: true)]
	private ?BlContact $blContact;

	#[ORM\Column(name: 'customer_name', type: 'string', nullable: false)]
	private string $customerName;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: false)]
	private \DateTimeInterface $startDate;

	#[ORM\ManyToOne(targetEntity: BlServiceType::class)]
	#[ORM\JoinColumn(name: 'bl_service_type_id', referencedColumnName: 'bl_service_type_id', nullable: false)]
	private BlServiceType $blServiceType;

	#[ORM\ManyToOne(targetEntity: BlCommunicationType::class)]
	#[ORM\JoinColumn(name: 'bl_communication_type_id', referencedColumnName: 'bl_communication_type_id', nullable: false)]
	private BlCommunicationType $blCommunicationType;

	#[ORM\Column(name: 'duration', type: 'integer', nullable: false)]
	private int $duration;

	#[ORM\ManyToOne(targetEntity: BlLanguage::class)]
	#[ORM\JoinColumn(name: 'bl_source_language_id', referencedColumnName: 'bl_language_id', nullable: false)]
	private BlLanguage $blSourceLanguage;

	#[ORM\ManyToOne(targetEntity: BlLanguage::class)]
	#[ORM\JoinColumn(name: 'bl_target_language_id', referencedColumnName: 'bl_language_id', nullable: false)]
	private BlLanguage $blTargetLanguage;

	#[ORM\Column(name: 'peer_rating_by_interpreter', type: 'integer', nullable: true)]
	private ?int $peerRatingByInterpreter;

	#[ORM\Column(name: 'call_quality_by_interpreter', type: 'integer', nullable: true)]
	private ?int $callQualityByInterpreter;

	#[ORM\Column(name: 'customer_amount', type: 'decimal', precision: 19, scale: 6, nullable: false)]
	private float $customerAmount;

	#[ORM\Column(name: 'queue_duration', type: 'integer', nullable: false)]
	private int $queueDuration;

	#[ORM\Column(name: 'toll_free_dialed', type: 'boolean', nullable: false)]
	private bool $tollFreeDialed;

	#[ORM\Column(name: 'is_backstop_answered', type: 'boolean', nullable: false)]
	private bool $isBackstopAnswered;

	#[ORM\ManyToOne(targetEntity: BlCustomer::class, inversedBy: 'blCalls')]
	#[ORM\JoinColumn(name: 'bl_customer_id', referencedColumnName: 'bl_customer_id', nullable: true)]
	private ?BlCustomer $blCustomer;

	#[ORM\Column(name: 'is_duration_update_pending', type: 'boolean', nullable: false)]
	private bool $isDurationUpdatePending;

	#[ORM\Column(name: 'status', type: 'string', nullable: false)]
	private string $status;

	#[ORM\Column(name: 'peer_rating_by_customer', type: 'integer', nullable: false)]
	private int $peerRatingByCustomer;

	#[ORM\Column(name: 'call_quality_by_customer', type: 'integer', nullable: false)]
	private int $callQualityByCustomer;

	#[ORM\Column(name: 'from_number', type: 'string', nullable: true)]
	private ?string $fromNumber;

	#[ORM\Column(name: 'third_party', type: 'string', nullable: true)]
	private ?string $thirdParty;

	#[ORM\Column(name: 'third_party_duration', type: 'integer', nullable: true)]
	private ?int $thirdPartyDuration;

	#[ORM\Column(name: 'operator_duration', type: 'integer', nullable: true)]
	private ?int $operatorDuration;

	#[ORM\Column(name: 'intake_duration', type: 'integer', nullable: true)]
	private ?int $intakeDuration;

	#[ORM\Column(name: 'interpreter_amount', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $interpreterAmount;

	#[ORM\Column(name: 'customer_unique_id', type: 'string', nullable: true)]
	private ?string $customerUniqueId;

	#[ORM\Column(name: 'customer_duration', type: 'integer', nullable: false)]
	private int $customerDuration;

	#[ORM\Column(name: 'additional', type: 'json', nullable: true)]
	private ?array $additional;

	#[ORM\Column(name: 'requester', type: 'string', nullable: true)]
	private ?string $requester;

	#[ORM\ManyToOne(targetEntity: BlRate::class)]
	#[ORM\JoinColumn(name: 'bl_rate_id', referencedColumnName: 'bl_rate_id', nullable: true)]
	private ?BlRate $blRate;

	#[ORM\Column(name: 'duration_minimal', type: 'boolean', nullable: false)]
	private bool $durationMinimal;

	#[ORM\Column(name: 'duration_seconds', type: 'integer', nullable: true)]
	private ?int $durationSeconds;

	#[ORM\Column(name: 'duration_minutes', type: 'integer', nullable: true)]
	private ?int $durationMinutes;

	#[ORM\Column(name: 'duration_hours', type: 'integer', nullable: true)]
	private ?int $durationHours;

	#[ORM\Column(name: 'routing_amount', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $routingAmount;

	#[ORM\Column(name: 'bl_amount', type: 'decimal', precision: 19, scale: 6, nullable: true)]
	private ?float $blAmount;

	#[ORM\Column(name: 'bl_provider_invoice_id', type: 'bigint', nullable: true)]
	private ?string $blProviderInvoiceId;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlCallId(): ?int
	{
		return $this->blCallId;
	}

	public function setBlCallId(int $blCallId): self
	{
		$this->blCallId = $blCallId;

		return $this;
	}

	public function getBlReferenceId(): ?int
	{
		return $this->blReferenceId;
	}

	public function setBlReferenceId(int $blReferenceId): self
	{
		$this->blReferenceId = $blReferenceId;

		return $this;
	}

	public function getIsCrowdClient(): ?bool
	{
		return $this->isCrowdClient;
	}

	public function setIsCrowdClient(bool $isCrowdClient): self
	{
		$this->isCrowdClient = $isCrowdClient;

		return $this;
	}

	public function getInterpreterName(): ?string
	{
		return $this->interpreterName;
	}

	public function setInterpreterName(?string $interpreterName): self
	{
		$this->interpreterName = $interpreterName;

		return $this;
	}

	public function getInterpreterReferralNumber(): ?string
	{
		return $this->interpreterReferralNumber;
	}

	public function setInterpreterReferralNumber(string $interpreterReferralNumber): self
	{
		$this->interpreterReferralNumber = $interpreterReferralNumber;

		return $this;
	}

	public function getCustomerName(): ?string
	{
		return $this->customerName;
	}

	public function setCustomerName(string $customerName): self
	{
		$this->customerName = $customerName;

		return $this;
	}

	public function getStartDate(): ?\DateTimeInterface
	{
		return $this->startDate;
	}

	public function setStartDate(\DateTimeInterface $startDate): self
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function getDuration(): ?int
	{
		return $this->duration;
	}

	public function setDuration(int $duration): self
	{
		$this->duration = $duration;

		return $this;
	}

	public function getPeerRatingByInterpreter(): ?int
	{
		return $this->peerRatingByInterpreter;
	}

	public function setPeerRatingByInterpreter(?int $peerRatingByInterpreter): self
	{
		$this->peerRatingByInterpreter = $peerRatingByInterpreter;

		return $this;
	}

	public function getCallQualityByInterpreter(): ?int
	{
		return $this->callQualityByInterpreter;
	}

	public function setCallQualityByInterpreter(?int $callQualityByInterpreter): self
	{
		$this->callQualityByInterpreter = $callQualityByInterpreter;

		return $this;
	}

	public function getCustomerAmount(): ?float
	{
		return $this->customerAmount;
	}

	public function setCustomerAmount(float $customerAmount): self
	{
		$this->customerAmount = $customerAmount;

		return $this;
	}

	public function getQueueDuration(): ?int
	{
		return $this->queueDuration;
	}

	public function setQueueDuration(int $queueDuration): self
	{
		$this->queueDuration = $queueDuration;

		return $this;
	}

	public function getTollFreeDialed(): ?bool
	{
		return $this->tollFreeDialed;
	}

	public function setTollFreeDialed(bool $tollFreeDialed): self
	{
		$this->tollFreeDialed = $tollFreeDialed;

		return $this;
	}

	public function getIsBackstopAnswered(): ?bool
	{
		return $this->isBackstopAnswered;
	}

	public function setIsBackstopAnswered(bool $isBackstopAnswered): self
	{
		$this->isBackstopAnswered = $isBackstopAnswered;

		return $this;
	}

	public function getIsDurationUpdatePending(): ?bool
	{
		return $this->isDurationUpdatePending;
	}

	public function setIsDurationUpdatePending(bool $isDurationUpdatePending): self
	{
		$this->isDurationUpdatePending = $isDurationUpdatePending;

		return $this;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getPeerRatingByCustomer(): ?int
	{
		return $this->peerRatingByCustomer;
	}

	public function setPeerRatingByCustomer(int $peerRatingByCustomer): self
	{
		$this->peerRatingByCustomer = $peerRatingByCustomer;

		return $this;
	}

	public function getCallQualityByCustomer(): ?int
	{
		return $this->callQualityByCustomer;
	}

	public function setCallQualityByCustomer(int $callQualityByCustomer): self
	{
		$this->callQualityByCustomer = $callQualityByCustomer;

		return $this;
	}

	public function getFromNumber(): ?string
	{
		return $this->fromNumber;
	}

	public function setFromNumber(?string $fromNumber): self
	{
		$this->fromNumber = $fromNumber;

		return $this;
	}

	public function getThirdParty(): ?string
	{
		return $this->thirdParty;
	}

	public function setThirdParty(?string $thirdParty): self
	{
		$this->thirdParty = $thirdParty;

		return $this;
	}

	public function getThirdPartyDuration(): ?int
	{
		return $this->thirdPartyDuration;
	}

	public function setThirdPartyDuration(?int $thirdPartyDuration): self
	{
		$this->thirdPartyDuration = $thirdPartyDuration;

		return $this;
	}

	public function getOperatorDuration(): ?int
	{
		return $this->operatorDuration;
	}

	public function setOperatorDuration(?int $operatorDuration): self
	{
		$this->operatorDuration = $operatorDuration;

		return $this;
	}

	public function getIntakeDuration(): ?int
	{
		return $this->intakeDuration;
	}

	public function setIntakeDuration(?int $intakeDuration): self
	{
		$this->intakeDuration = $intakeDuration;

		return $this;
	}

	public function getInterpreterAmount(): ?float
	{
		return $this->interpreterAmount;
	}

	public function setInterpreterAmount(?float $interpreterAmount): self
	{
		$this->interpreterAmount = $interpreterAmount;

		return $this;
	}

	public function getCustomerUniqueId(): ?string
	{
		return $this->customerUniqueId;
	}

	public function setCustomerUniqueId(?string $customerUniqueId): self
	{
		$this->customerUniqueId = $customerUniqueId;

		return $this;
	}

	public function getBlContact(): ?BlContact
	{
		return $this->blContact;
	}

	public function setBlContact(?BlContact $blContact): self
	{
		$this->blContact = $blContact;

		return $this;
	}

	public function getBlServiceType(): ?BlServiceType
	{
		return $this->blServiceType;
	}

	public function setBlServiceType(?BlServiceType $blServiceType): self
	{
		$this->blServiceType = $blServiceType;

		return $this;
	}

	public function getBlCommunicationType(): ?BlCommunicationType
	{
		return $this->blCommunicationType;
	}

	public function setBlCommunicationType(?BlCommunicationType $blCommunicationType): self
	{
		$this->blCommunicationType = $blCommunicationType;

		return $this;
	}

	public function getBlSourceLanguage(): ?BlLanguage
	{
		return $this->blSourceLanguage;
	}

	public function setBlSourceLanguage(?BlLanguage $blSourceLanguage): self
	{
		$this->blSourceLanguage = $blSourceLanguage;

		return $this;
	}

	public function getBlTargetLanguage(): ?BlLanguage
	{
		return $this->blTargetLanguage;
	}

	public function setBlTargetLanguage(?BlLanguage $blTargetLanguage): self
	{
		$this->blTargetLanguage = $blTargetLanguage;

		return $this;
	}

	public function getBlCustomer(): ?BlCustomer
	{
		return $this->blCustomer;
	}

	public function setBlCustomer(?BlCustomer $blCustomer): self
	{
		$this->blCustomer = $blCustomer;

		return $this;
	}

	public function getCustomerDuration(): ?int
	{
		return $this->customerDuration;
	}

	public function setCustomerDuration(int $customerDuration): self
	{
		$this->customerDuration = $customerDuration;

		return $this;
	}

	public function getAdditional(): ?array
	{
		return $this->additional;
	}

	public function setAdditional(?array $additional): self
	{
		$this->additional = $additional;

		return $this;
	}

	public function getRequester(): ?string
	{
		return $this->requester;
	}

	public function setRequester(?string $requester): self
	{
		$this->requester = $requester;

		return $this;
	}

	public function getBlRate(): ?BlRate
	{
		return $this->blRate;
	}

	public function setBlRate(?BlRate $blRate): self
	{
		$this->blRate = $blRate;

		return $this;
	}

	public function getDurationMinimal(): ?bool
	{
		return $this->durationMinimal;
	}

	public function setDurationMinimal(bool $durationMinimal): self
	{
		$this->durationMinimal = $durationMinimal;

		return $this;
	}

	public function getDurationSeconds(): ?int
	{
		return $this->durationSeconds;
	}

	public function setDurationSeconds(?int $durationSeconds): self
	{
		$this->durationSeconds = $durationSeconds;

		return $this;
	}

	public function getDurationMinutes(): ?int
	{
		return $this->durationMinutes;
	}

	public function setDurationMinutes(?int $durationMinutes): self
	{
		$this->durationMinutes = $durationMinutes;

		return $this;
	}

	public function getDurationHours(): ?int
	{
		return $this->durationHours;
	}

	public function setDurationHours(?int $durationHours): self
	{
		$this->durationHours = $durationHours;

		return $this;
	}

	public function getRoutingAmount(): ?float
	{
		return $this->routingAmount;
	}

	public function setRoutingAmount(?float $routingAmount): self
	{
		$this->routingAmount = $routingAmount;

		return $this;
	}

	public function getBlAmount(): ?float
	{
		return $this->blAmount;
	}

	public function setBlAmount(?float $blAmount): self
	{
		$this->blAmount = $blAmount;

		return $this;
	}

	public function isIsCrowdClient(): ?bool
	{
		return $this->isCrowdClient;
	}

	public function isTollFreeDialed(): ?bool
	{
		return $this->tollFreeDialed;
	}

	public function isIsBackstopAnswered(): ?bool
	{
		return $this->isBackstopAnswered;
	}

	public function isIsDurationUpdatePending(): ?bool
	{
		return $this->isDurationUpdatePending;
	}

	public function isDurationMinimal(): ?bool
	{
		return $this->durationMinimal;
	}

	public function getBlProviderInvoiceId(): ?string
	{
		return $this->blProviderInvoiceId;
	}

	public function setBlProviderInvoiceId(?string $blProviderInvoiceId): self
	{
		$this->blProviderInvoiceId = $blProviderInvoiceId;

		return $this;
	}
}
