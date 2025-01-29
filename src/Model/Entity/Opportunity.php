<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'opportunity')]
#[ORM\Entity]
class Opportunity implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'opportunity_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'opportunity_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\ManyToOne(targetEntity: CustomerPerson::class)]
	#[ORM\JoinColumn(name: 'customer_contact_person', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?CustomerPerson $customerPerson;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'sales_person_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $salesPerson;

	#[ORM\Column(name: 'expected_close_date', type: 'datetime', nullable: false)]
	private ?\DateTimeInterface $expectedCloseDate;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'first_choosed_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $firstChoosedCurrency;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'notes', type: 'text', nullable: true)]
	private ?string $notes;

	#[ORM\Column(name: 'started_on', type: 'datetime', nullable: false)]
	private ?\DateTimeInterface $startedOn;

	#[ORM\Column(name: 'pessimistic_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $pessimisticAmount;

	#[ORM\Column(name: 'optimistic_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $optimisticAmount;

	#[ORM\Column(name: 'pessimistic_net_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $pessimisticNetAmount;

	#[ORM\Column(name: 'optimistic_net_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $optimisticNetAmount;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'optimistic_net_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $optimisticNetCurrency;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'pessimistic_net_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $pessimisticNetCurrency;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'optimistic_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $optimisticCurrency;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'pessimistic_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $pessimisticCurrency;

	#[ORM\Column(name: 'optimistic_status_id', type: 'bigint', nullable: true)]
	private ?string $optimisticStatusId;

	#[ORM\Column(name: 'pessimistic_status_id', type: 'bigint', nullable: true)]
	private ?string $pessimisticStatusId;

	#[ORM\Column(name: 'most_probable_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $mostProbableAmount;

	#[ORM\Column(name: 'most_probable_net_amount', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $mostProbableNetAmount;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'most_probable_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $mostProbableCurrency;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'most_probable_net_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
	private ?Currency $mostProbableNetCurrency;

	#[ORM\Column(name: 'most_probable_status_id', type: 'bigint', nullable: true)]
	private ?string $mostProbableStatusId;
}
