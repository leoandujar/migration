<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'opportunity_offer')]
#[ORM\Entity]
class OpportunityOffer implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'opportunity_offer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'opportunity_offer_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'close_reason', type: 'text', nullable: true)]
	private ?string $closeReason;

	#[ORM\Column(name: 'amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private float $amount;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $currency;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'probability_percent', type: 'integer', nullable: true)]
	private ?int $probabilityPercent;

	#[ORM\ManyToOne(targetEntity: Quote::class)]
	#[ORM\JoinColumn(name: 'quote_id', referencedColumnName: 'quote_id', nullable: true)]
	private ?Quote $quote;

	#[ORM\Column(name: 'synchronized_with_quote', type: 'boolean', nullable: false)]
	private bool $synchronizedWithQuote;

	#[ORM\ManyToOne(targetEntity: Opportunity::class)]
	#[ORM\JoinColumn(name: 'opportunity_id', referencedColumnName: 'opportunity_id', nullable: true)]
	private ?Opportunity $opportunity;

	#[ORM\ManyToOne(targetEntity: OpportunityStatus::class)]
	#[ORM\JoinColumn(name: 'opportunity_status_id', referencedColumnName: 'opportunity_status_id', nullable: false)]
	private OpportunityStatus $opportunityStatus;

	#[ORM\Column(name: 'close_reason_type_id', type: 'bigint', nullable: true)]
	private ?string $closeReasonTypeId;
}
