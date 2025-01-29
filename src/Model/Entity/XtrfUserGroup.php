<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'xtrf_user_group')]
#[ORM\UniqueConstraint(name: 'xtrf_user_group_name_key', columns: ['name'])]
#[ORM\UniqueConstraint(name: 'xtrf_user_group_customer_status_key', columns: ['customer_status'])]
#[ORM\Entity]
class XtrfUserGroup implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'xtrf_user_group_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'xtrf_user_group_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTime $lastModificationDate;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
	private ?bool $active;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'localized_entity', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedEntity;

	#[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
	private bool $defaultEntity;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
	private bool $preferedEntity;

	#[ORM\Column(name: 'customer_status', type: 'string', nullable: true)]
	private ?string $customerStatus;

	#[ORM\Column(name: 'person_group', type: 'boolean', nullable: false)]
	private bool $personGroup;

	#[ORM\Column(name: 'usage_group', type: 'string', nullable: false)]
	private string $usageGroup;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'leader_id', referencedColumnName: 'xtrf_user_id', nullable: true)]
	private ?User $leader;

	#[ORM\Column(name: 'system_roles', type: 'string', nullable: true)]
	private ?string $systemRoles;
}
