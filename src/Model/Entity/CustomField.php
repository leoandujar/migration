<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'custom_fields')]
#[ORM\Entity]
class CustomField implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'custom_fields_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'custom_fields_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'ownertype', type: 'string', length: 31, nullable: false)]
	private string $ownertype;

	#[ORM\Column(name: 'checkbox_field_1', type: 'boolean', nullable: true)]
	private ?bool $checkboxField1;

	#[ORM\Column(name: 'checkbox_field_2', type: 'boolean', nullable: true)]
	private ?bool $checkboxField2;

	#[ORM\Column(name: 'checkbox_field_3', type: 'boolean', nullable: true)]
	private ?bool $checkboxField3;

	#[ORM\Column(name: 'checkbox_field_4', type: 'boolean', nullable: true)]
	private ?bool $checkboxField4;

	#[ORM\Column(name: 'checkbox_field_5', type: 'boolean', nullable: true)]
	private ?bool $checkboxField5;

	#[ORM\Column(name: 'checkbox_field_6', type: 'boolean', nullable: true)]
	private ?bool $checkboxField6;

	#[ORM\Column(name: 'checkbox_field_7', type: 'boolean', nullable: true)]
	private ?bool $checkboxField7;

	#[ORM\Column(name: 'checkbox_field_8', type: 'boolean', nullable: true)]
	private ?bool $checkboxField8;

	#[ORM\Column(name: 'checkbox_field_9', type: 'boolean', nullable: true)]
	private ?bool $checkboxField9;

	#[ORM\Column(name: 'checkbox_field_10', type: 'boolean', nullable: true)]
	private ?bool $checkboxField10;

	#[ORM\Column(name: 'date_field_1', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField1;

	#[ORM\Column(name: 'date_field_2', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField2;

	#[ORM\Column(name: 'date_field_3', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField3;

	#[ORM\Column(name: 'date_field_4', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField4;

	#[ORM\Column(name: 'date_field_5', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField5;

	#[ORM\Column(name: 'date_field_6', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField6;

	#[ORM\Column(name: 'date_field_7', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField7;

	#[ORM\Column(name: 'date_field_8', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField8;

	#[ORM\Column(name: 'date_field_9', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField9;

	#[ORM\Column(name: 'date_field_10', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dateField10;

	#[ORM\Column(name: 'number_field_1', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField1;

	#[ORM\Column(name: 'number_field_2', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField2;

	#[ORM\Column(name: 'number_field_3', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField3;

	#[ORM\Column(name: 'number_field_4', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField4;

	#[ORM\Column(name: 'number_field_5', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField5;

	#[ORM\Column(name: 'number_field_6', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField6;

	#[ORM\Column(name: 'number_field_7', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField7;

	#[ORM\Column(name: 'number_field_8', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField8;

	#[ORM\Column(name: 'number_field_9', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField9;

	#[ORM\Column(name: 'number_field_10', type: 'decimal', precision: 40, scale: 10, nullable: true)]
	private ?float $numberField10;

	#[ORM\Column(name: 'text_field_1', type: 'text', nullable: true)]
	private ?string $textField1;

	#[ORM\Column(name: 'text_field_2', type: 'text', nullable: true)]
	private ?string $textField2;

	#[ORM\Column(name: 'text_field_3', type: 'text', nullable: true)]
	private ?string $textField3;

	#[ORM\Column(name: 'text_field_4', type: 'text', nullable: true)]
	private ?string $textField4;

	#[ORM\Column(name: 'text_field_5', type: 'text', nullable: true)]
	private ?string $textField5;

	#[ORM\Column(name: 'text_field_6', type: 'text', nullable: true)]
	private ?string $textField6;

	#[ORM\Column(name: 'text_field_7', type: 'text', nullable: true)]
	private ?string $textField7;

	#[ORM\Column(name: 'text_field_8', type: 'text', nullable: true)]
	private ?string $textField8;

	#[ORM\Column(name: 'text_field_9', type: 'text', nullable: true)]
	private ?string $textField9;

	#[ORM\Column(name: 'text_field_10', type: 'text', nullable: true)]
	private ?string $textField10;

	#[ORM\Column(name: 'select_field_1', type: 'text', nullable: true)]
	private ?string $selectField1;

	#[ORM\Column(name: 'select_field_2', type: 'text', nullable: true)]
	private ?string $selectField2;

	#[ORM\Column(name: 'select_field_3', type: 'text', nullable: true)]
	private ?string $selectField3;

	#[ORM\Column(name: 'select_field_4', type: 'text', nullable: true)]
	private ?string $selectField4;

	#[ORM\Column(name: 'select_field_5', type: 'text', nullable: true)]
	private ?string $selectField5;

	#[ORM\Column(name: 'select_field_6', type: 'text', nullable: true)]
	private ?string $selectField6;

	#[ORM\Column(name: 'select_field_7', type: 'text', nullable: true)]
	private ?string $selectField7;

	#[ORM\Column(name: 'select_field_8', type: 'text', nullable: true)]
	private ?string $selectField8;

	#[ORM\Column(name: 'select_field_9', type: 'text', nullable: true)]
	private ?string $selectField9;

	#[ORM\Column(name: 'select_field_10', type: 'text', nullable: true)]
	private ?string $selectField10;

	#[ORM\Column(name: 'multi_select_field_1', type: 'text', nullable: true)]
	private ?string $multiSelectField1;

	#[ORM\Column(name: 'multi_select_field_2', type: 'text', nullable: true)]
	private ?string $multiSelectField2;

	#[ORM\Column(name: 'multi_select_field_3', type: 'text', nullable: true)]
	private ?string $multiSelectField3;

	#[ORM\Column(name: 'multi_select_field_4', type: 'text', nullable: true)]
	private ?string $multiSelectField4;

	#[ORM\Column(name: 'multi_select_field_5', type: 'text', nullable: true)]
	private ?string $multiSelectField5;

	#[ORM\Column(name: 'multi_select_field_6', type: 'text', nullable: true)]
	private ?string $multiSelectField6;

	#[ORM\Column(name: 'multi_select_field_7', type: 'text', nullable: true)]
	private ?string $multiSelectField7;

	#[ORM\Column(name: 'multi_select_field_8', type: 'text', nullable: true)]
	private ?string $multiSelectField8;

	#[ORM\Column(name: 'multi_select_field_9', type: 'text', nullable: true)]
	private ?string $multiSelectField9;

	#[ORM\Column(name: 'multi_select_field_10', type: 'text', nullable: true)]
	private ?string $multiSelectField10;
}
