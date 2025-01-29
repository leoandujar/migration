<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'external_system_project')]
#[ORM\Entity]
class ExternalSystemProject implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'external_system_project_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'external_system_project_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'project_id', type: 'string', nullable: true)]
	private ?string $projectId;

	#[ORM\Column(name: 'project_name', type: 'string', length: 4096, nullable: true)]
	private ?string $projectName;

	#[ORM\Column(name: 'resources', type: 'text', nullable: true)]
	private ?string $resources;

	#[ORM\Column(name: 'write_tmp_term', type: 'string', length: 4095, nullable: true)]
	private ?string $writeTmpTerm;

	#[ORM\Column(name: 'write_tmp_tm', type: 'string', length: 4095, nullable: true)]
	private ?string $writeTmpTm;

	#[ORM\Column(name: 'input_hash', type: 'text', nullable: true)]
	private ?string $inputHash;

	#[ORM\Column(name: 'external_system_detailed_status', type: 'text', nullable: true)]
	private ?string $externalSystemDetailedStatus;

	#[ORM\Column(name: 'external_system_status', type: 'text', nullable: true)]
	private ?string $externalSystemStatus;

	#[ORM\Column(name: 'external_system_status_params', type: 'text', nullable: true)]
	private ?string $externalSystemStatusParams;

	#[ORM\Column(name: 'not_owned_by_xtrf', type: 'boolean', nullable: true)]
	private ?bool $notOwnedByXtrf;

	#[ORM\Column(name: 'mt_engine', type: 'string', length: 4095, nullable: true)]
	private ?string $mtEngine;

	#[ORM\Column(name: 'status', type: 'string', nullable: true)]
	private ?string $status;

	#[ORM\Column(name: 'status_in_progress', type: 'boolean', nullable: true)]
	private ?bool $statusInProgress;

	#[ORM\Column(name: 'external_system_id', type: 'bigint', nullable: true)]
	private ?string $externalSystemId;

	#[ORM\ManyToOne(targetEntity: Activity::class)]
	#[ORM\JoinColumn(name: 'activity_with_all_files_id', referencedColumnName: 'activity_id', nullable: true)]
	private ?Activity $activityWithAllFiles;
}
