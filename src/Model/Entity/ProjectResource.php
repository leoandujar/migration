<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'project_resource')]
#[ORM\Entity]
class ProjectResource implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'project_resource_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'project_resource_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'description', type: 'text', nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'name', type: 'text', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'resource_url', type: 'text', nullable: false)]
	private string $resourceUrl;

	#[ORM\Column(name: '_default', type: 'boolean', nullable: true)]
	private ?bool $default;

	#[ORM\Column(name: 'inner_type', type: 'string', nullable: true)]
	private ?string $innerType;

	#[ORM\Column(name: 'project_resource_type', type: 'string', nullable: true)]
	private ?string $projectResourceType;

	#[ORM\Column(name: 'resource_type', type: 'string', nullable: true)]
	private ?string $resourceType;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: CustomerPriceProfile::class)]
	#[ORM\JoinColumn(name: 'customer_price_profile_id', referencedColumnName: 'customer_price_profile_id', nullable: true)]
	private ?CustomerPriceProfile $customerPortalPriceProfile;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;
}
