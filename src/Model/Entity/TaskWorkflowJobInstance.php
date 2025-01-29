<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task_workflow_job_instance')]
#[ORM\UniqueConstraint(name: 'task_workflow_job_instance_workflowjobinstances_workflow_jo_key', columns: ['workflowjobinstances_workflow_job_instance_id'])]
#[ORM\Entity]
class TaskWorkflowJobInstance implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'jobInstances')]
	#[ORM\JoinColumn(name: 'task_task_id', referencedColumnName: 'task_id', nullable: false)]
	private Task $task;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: WorkflowJobInstance::class, inversedBy: 'tasks')]
	#[ORM\JoinColumn(name: 'workflowjobinstances_workflow_job_instance_id', referencedColumnName: 'workflow_job_instance_id', nullable: false)]
	private WorkflowJobInstance $workflow;

	public function getTask(): ?Task
	{
		return $this->task;
	}

	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}

	public function getWorkflow(): ?WorkflowJobInstance
	{
		return $this->workflow;
	}

	public function setWorkflow(?WorkflowJobInstance $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}
}
