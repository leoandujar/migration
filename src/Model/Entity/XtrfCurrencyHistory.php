<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'xtrf_currency_history')]
#[ORM\Entity]
class XtrfCurrencyHistory implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'xtrf_currency_history_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'xtrf_currency_history_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'exchange_rate', type: 'decimal', precision: 19, scale: 5, nullable: false)]
	private string $exchangeRate;

	#[ORM\Column(name: 'from_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $fromDate;

	#[ORM\Column(name: 'origin_details', type: 'string', nullable: false)]
	private string $originDetails;

	#[ORM\Column(name: 'publication_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $publicationDate;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $type;

	#[ORM\ManyToOne(targetEntity: Currency::class)]
	#[ORM\JoinColumn(name: 'xtrf_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: false)]
	private $currency;
}
