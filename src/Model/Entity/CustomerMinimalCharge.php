<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'customer_minimal_charge')]
#[ORM\Entity]
class CustomerMinimalCharge implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'customer_minimal_charge_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'customer_minimal_charge_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'rate', type: 'decimal', precision: 16, scale: 2, nullable: false)]
	private float $rate;

	#[ORM\ManyToOne(targetEntity: CustomerLanguageCombination::class)]
	#[ORM\JoinColumn(name: 'customer_language_combination_id', referencedColumnName: 'customer_language_combination_id', nullable: false)]
	private CustomerLanguageCombination $customerLanguageCombination;

	#[ORM\ManyToOne(targetEntity: CustomerPriceProfile::class)]
	#[ORM\JoinColumn(name: 'customer_price_profile_id', referencedColumnName: 'customer_price_profile_id', nullable: true)]
	private ?CustomerPriceProfile $customerPriceProfile;
}
