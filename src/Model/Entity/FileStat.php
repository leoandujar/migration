<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'file_stats')]
#[ORM\Entity]
class FileStat implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'file_stat_id', type: 'bigint')]
	private string $fileStatId;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'file_stat_type', type: 'string')]
	private string $fileStatType;

	#[ORM\Column(name: 'origin', type: 'string', nullable: false)]
	private string $origin;

	#[ORM\Column(name: 'value', type: 'bigint', nullable: true)]
	private ?string $value;
}
