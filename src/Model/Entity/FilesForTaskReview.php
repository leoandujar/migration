<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'files_for_task_review')]
#[ORM\Entity]
class FilesForTaskReview implements EntityInterface
{
	#[ORM\Id]
	#[ORM\OneToOne(targetEntity: TaskReview::class, inversedBy: 'fileForTaskReview')]
	#[ORM\JoinColumn(name: 'task_review_id', referencedColumnName: 'task_review_id', nullable: false)]
	private TaskReview $taskReview;

	#[ORM\Id]
	#[ORM\Column(name: 'file_name', type: 'string', nullable: false)]
	private string $fileName;

	#[ORM\ManyToOne(targetEntity: WorkflowJobFile::class)]
	#[ORM\JoinColumn(name: 'workflow_job_file_id', referencedColumnName: 'workflow_job_file_id', nullable: false)]
	private WorkflowJobFile $workflowJobFile;

	public function getFileName(): ?string
	{
		return $this->fileName;
	}

	public function getTaskReview(): ?TaskReview
	{
		return $this->taskReview;
	}

	/**
	 * @return mixed
	 */
	public function setTaskReview(TaskReview $taskReview): self
	{
		$this->taskReview = $taskReview;

		return $this;
	}

	public function getWorkflowJobFile(): ?WorkflowJobFile
	{
		return $this->workflowJobFile;
	}

	/**
	 * @return mixed
	 */
	public function setWorkflowJobFile(?WorkflowJobFile $workflowJobFile): self
	{
		$this->workflowJobFile = $workflowJobFile;

		return $this;
	}
}
