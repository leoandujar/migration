<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'tm_rates_item')]
#[ORM\Entity]
class TmRateItem implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: TmRate::class)]
	#[ORM\JoinColumn(name: 'tm_rates_id', referencedColumnName: 'tm_rates_id', nullable: false)]
	private TmRate $tmRates;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'match_type', type: 'string', nullable: false)]
	private string $matchType;

	#[ORM\Column(name: 'rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private float $rate;
}
