<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'workflow_job_phase')]
#[ORM\Entity]
class WorkflowJobPhase implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\OneToOne(targetEntity: WorkflowJob::class, cascade: ['persist'], inversedBy: 'tasks')]
	#[ORM\JoinColumn(name: 'workflow_job_id', referencedColumnName: 'workflow_job_id', nullable: false)]
	private WorkflowJob $workflowJob;

	#[ORM\Column(name: 'phase', type: 'string', nullable: true)]
	private ?string $phase;
}
