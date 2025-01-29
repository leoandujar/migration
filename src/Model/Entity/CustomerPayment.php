<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_payment')]
#[ORM\Entity]
class CustomerPayment implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_payment_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_payment_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'accepted_value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $acceptedValue;

	#[ORM\Column(name: 'payment_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $paymentDate;

	#[ORM\Column(name: 'received_value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $receivedValue;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'accepted_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $acceptedCurrency;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'received_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private Currency $receivedCurrency;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\ManyToOne(targetEntity: Account::class)]
	#[ORM\JoinColumn(name: 'payment_method_id', referencedColumnName: 'account_id', nullable: true)]
	private ?Account $paymentMethod;

	#[ORM\ManyToOne(targetEntity: CustomerPayment::class)]
	#[ORM\JoinColumn(name: 'prepayment_clearing_correlated_customer_payment_id', referencedColumnName: 'customer_payment_id', nullable: true)]
	private ?CustomerPayment $prepaymentClearingCorrelatedCustomerPayment;

	#[ORM\Column(name: 'financial_system_id', type: 'bigint', nullable: true)]
	private ?string $financialSystemId;

	#[ORM\Column(name: 'financial_system_payment_id', type: 'string', nullable: true)]
	private ?string $financialSystemPaymentId;

	#[ORM\Column(name: 'due_date', type: 'date', nullable: true)]
	private ?\DateTimeInterface $dueDate;

	#[ORM\Column(name: 'irrecoverable', type: 'boolean', nullable: true)]
	private ?bool $irrecoverable;

	#[ORM\Column(name: 'percent_of_total', type: 'decimal', precision: 10, scale: 2, nullable: true)]
	private ?float $percentOfTotal;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 16, scale: 2, nullable: true)]
	private ?float $value;

	#[ORM\Column(name: 'prepayment_clearing_mode', type: 'string', nullable: true)]
	private ?string $prepaymentClearingMode;

	#[ORM\Column(name: 'charge_type_id', type: 'bigint', nullable: true)]
	private ?string $chargeTypeId;
}
