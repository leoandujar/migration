<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'workflow_job_instance')]
#[ORM\Entity]
class WorkflowJobInstance implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'workflow_job_instance_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'workflow_job_instance_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\Column(name: 'jobinsttype', type: 'string', length: 31, nullable: false)]
	private string $jobinsttype;

	#[ORM\Column(name: 'job_position', type: 'integer', nullable: true)]
	private ?int $jobPosition;

	#[ORM\Column(name: 'all_bundles_task_id', type: 'bigint', nullable: true)]
	private ?string $allBundlesTaskId;

	#[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'jobs')]
	#[ORM\JoinColumn(name: 'all_bundles_activity_id', referencedColumnName: 'activity_id', nullable: true)]
	private ?Activity $allBundlesActivity;

	#[ORM\OneToOne(targetEntity: WorkflowJob::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'workflow_job_id', referencedColumnName: 'workflow_job_id', nullable: true)]
	private ?WorkflowJob $workflowJob;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'jobs')]
	#[ORM\JoinColumn(name: 'all_bundles_activity_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $allBundlesTask;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'jobs')]
	#[ORM\JoinColumn(name: 'project_task_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $projectTask;

	#[ORM\ManyToMany(targetEntity: TaskWorkflowJobInstance::class, mappedBy: 'workflow', cascade: ['persist'])]
	private mixed $tasks;
}
