<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_payment_item')]
#[ORM\Entity]
class CustomerPaymentItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_payment_item_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_payment_item_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'value', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $value;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\ManyToOne(targetEntity: CustomerCharge::class)]
	#[ORM\JoinColumn(name: 'customer_charge_id', referencedColumnName: 'customer_charge_id', nullable: false)]
	private CustomerCharge $customerCharge;

	#[ORM\ManyToOne(targetEntity: CustomerPayment::class)]
	#[ORM\JoinColumn(name: 'customer_payment_id', referencedColumnName: 'customer_payment_id', nullable: false)]
	private CustomerPayment $customerPayment;
}
