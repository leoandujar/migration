<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'provider_billing_data')]
#[ORM\Entity]
class ProviderBillingData implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'provider_billing_data', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'pesel', type: 'string', nullable: true)]
	private ?string $pesel;

	#[ORM\Column(name: 'correspondence_address', type: 'string', nullable: true)]
	private ?string $correspondenceAddress;

	#[ORM\Column(name: 'correspondence_address_2', type: 'string', nullable: true)]
	private ?string $correspondenceAddress2;

	#[ORM\Column(name: 'correspondence_city', type: 'string', nullable: true)]
	private ?string $correspondenceCity;

	#[ORM\Column(name: 'correspondence_dependent_locality', type: 'string', nullable: true)]
	private ?string $correspondenceDependentLocality;

	#[ORM\Column(name: 'correspondence_sorting_code', type: 'string', nullable: true)]
	private ?string $correspondenceSortingCode;

	#[ORM\Column(name: 'correspondence_zipcode', type: 'string', nullable: true)]
	private ?string $correspondenceZipcode;

	#[ORM\Column(name: 'birth_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $birthDate;

	#[ORM\Column(name: 'birth_place', type: 'string', nullable: true)]
	private ?string $birthPlace;

	#[ORM\Column(name: 'certificate_number', type: 'string', nullable: true)]
	private ?string $certificateNumber;

	#[ORM\Column(name: 'employed', type: 'boolean', nullable: true)]
	private ?bool $employed;

	#[ORM\Column(name: 'employer_name', type: 'string', nullable: true)]
	private ?string $employerName;

	#[ORM\Column(name: 'father_name', type: 'string', nullable: true)]
	private ?string $fatherName;

	#[ORM\Column(name: 'mother_maiden_name', type: 'string', nullable: true)]
	private ?string $motherMaidenName;

	#[ORM\Column(name: 'mother_name', type: 'string', nullable: true)]
	private ?string $motherName;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'social_security', type: 'string', nullable: true)]
	private ?string $socialSecurity;

	#[ORM\Column(name: 'special_instructions', type: 'text', nullable: true)]
	private ?string $specialInstructions;

	#[ORM\Column(name: 'tax_no_1', type: 'string', nullable: true)]
	private ?string $taxNo1;

	#[ORM\Column(name: 'type', type: 'string', nullable: true)]
	private ?string $type;

	#[ORM\ManyToOne(targetEntity: Country::class)]
	#[ORM\JoinColumn(name: 'correspondence_country_id', referencedColumnName: 'country_id', nullable: true)]
	private ?Country $correspondenceCountry;

	#[ORM\ManyToOne(targetEntity: Province::class)]
	#[ORM\JoinColumn(name: 'correspondence_province_id', referencedColumnName: 'province_id', nullable: true)]
	private ?Province $correspondenceProvince;

	#[ORM\Column(name: 'treasury_office_id', type: 'bigint', nullable: true)]
	private ?string $treasuryOfficeId;
}
