<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'charge_definition')]
#[ORM\Entity]
class ChargeDefinition implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'charge_definition_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'charge_definition_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'date_reference', type: 'string', nullable: true)]
	private ?string $dateReference;

	#[ORM\Column(name: 'days', type: 'smallint', nullable: true)]
	private ?int $days;

	#[ORM\Column(name: 'end_of_month', type: 'boolean', nullable: true)]
	private ?bool $endOfMonth;

	#[ORM\Column(name: 'months', type: 'smallint', nullable: true)]
	private ?int $months;

	#[ORM\Column(name: 'percent_of_invoice_total', type: 'decimal', precision: 8, scale: 6, nullable: false)]
	private float $percentOfInvoiceTotal;

	#[ORM\ManyToOne(targetEntity: ChargeType::class)]
	#[ORM\JoinColumn(name: 'charge_type_id', referencedColumnName: 'charge_type_id', nullable: false)]
	private ChargeType $chargeType;

	#[ORM\ManyToOne(targetEntity: PaymentCondition::class)]
	#[ORM\JoinColumn(name: 'payment_conditions_id', referencedColumnName: 'payment_conditions_id', nullable: false)]
	private PaymentCondition $paymentCondition;
}
