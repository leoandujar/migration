<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'task_review')]
#[ORM\Entity]
class TaskReview implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'task_review_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'task_review_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'bigint', nullable: false)]
	private string $version;

	#[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'taskForReview')]
	#[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'task_id', nullable: true)]
	private ?Task $task;

	#[ORM\OneToOne(targetEntity: FilesForTaskReview::class, mappedBy: 'taskReview')]
	private FilesForTaskReview $fileForTaskReview;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getVersion(): ?string
	{
		return $this->version;
	}

	public function setVersion(string $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getTask(): ?Task
	{
		return $this->task;
	}

	public function setTask(?Task $task): self
	{
		$this->task = $task;

		return $this;
	}

	public function getFileForTaskReview(): ?FilesForTaskReview
	{
		return $this->fileForTaskReview;
	}

	public function setFileForTaskReview(?FilesForTaskReview $fileForTaskReview): self
	{
		$this->fileForTaskReview = $fileForTaskReview;

		// set (or unset) the owning side of the relation if necessary
		$newTaskReview = null === $fileForTaskReview ? null : $this;
		if ($fileForTaskReview->getTaskReview() !== $newTaskReview) {
			$fileForTaskReview->setTaskReview($newTaskReview);
		}

		return $this;
	}
}
