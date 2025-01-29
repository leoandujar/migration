<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'payment_conditions')]
#[ORM\UniqueConstraint(name: 'payment_conditions_name_scope_id_number_key', columns: ['name', 'scope', 'id_number'])]
#[ORM\Entity]
class PaymentCondition implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'payment_conditions_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'payment_conditions_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'scope', type: 'string', length: 31, nullable: false)]
	private string $scope;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

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

	#[ORM\Column(name: 'id_number', type: 'string', nullable: false)]
	private string $idNumber;

	#[ORM\Column(name: 'gc_system_configuration_id', type: 'bigint', nullable: true)]
	private ?string $gcSystemConfigurationId;

	#[ORM\Column(name: 'gp_system_configuration_id', type: 'bigint', nullable: true)]
	private ?string $gpSystemConfigurationId;

	#[ORM\Column(name: 'localized_description_expression', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $localizedDescriptionExpression;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\ManyToOne(targetEntity: Provider::class)]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $provider;
}
