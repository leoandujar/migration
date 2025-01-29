<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'provider_charge')]
#[ORM\Entity]
class ProviderCharge implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'provider_charge_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'provider_charge_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'due_date', type: 'date', nullable: false)]
	private \DateTimeInterface $dueDate;

	#[ORM\Column(name: 'irrecoverable', type: 'boolean', nullable: true)]
	private ?bool $irrecoverable;

	#[ORM\Column(name: 'percent_of_total', type: 'decimal', precision: 8, scale: 6, nullable: true)]
	private ?float $percentOfTotal;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $value;

	#[ORM\ManyToOne(targetEntity: ChargeType::class)]
	#[ORM\JoinColumn(name: 'charge_type_id', referencedColumnName: 'charge_type_id', nullable: false)]
	private ChargeType $chargeType;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $currency;

	#[ORM\ManyToOne(targetEntity: ProviderInvoice::class, inversedBy: 'providerCharge')]
	#[ORM\JoinColumn(name: 'provider_invoice_id', referencedColumnName: 'provider_invoice_id', nullable: true)]
	private ?ProviderInvoice $providerInvoice;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'jobs')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: false)]
	private Provider $provider;
}
