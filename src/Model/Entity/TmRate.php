<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'tm_rates')]
#[ORM\Entity]
class TmRate implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'tm_rates_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'tm_rates_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'discriminator', type: 'string', length: 31, nullable: false)]
	private string $discriminator;

	#[ORM\Column(name: 'cat_tool', type: 'string', nullable: true)]
	private ?string $catTool;

	#[ORM\Column(name: 'rate_type', type: 'string', nullable: true)]
	private ?string $rateType;

	/**
	 * @var string|null
	 */
	#[ORM\Column(name: 'system_configuration_id', type: 'bigint', nullable: true)]
	private ?string $systemConfigurationId;

	/**
	 * @var string|null
	 */
	#[ORM\Column(name: 'provider_price_profile_id', type: 'bigint', nullable: true)]
	private ?string $providerPriceProfileId;

	#[ORM\ManyToOne(targetEntity: CustomerPriceProfile::class)]
	#[ORM\JoinColumn(name: 'customer_price_profile_id', referencedColumnName: 'customer_price_profile_id', nullable: true)]
	private ?CustomerPriceProfile $customerPriceProfile;
}
