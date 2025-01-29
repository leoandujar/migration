<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'av_workflow_params')]
#[ORM\Entity]
class WFParams
{
	public const NOTIFICATION_TYPE_TEAM = 1;
	public const NOTIFICATION_TYPE_EMAIL = 2;
	public const NOTIFICATION_TYPE_SMS = 3;

	public const DISK_AWS_INVOICES = 'aws_invoices';
	public const DISK_AWS_PROJECTS = 'aws_projects';
	public const DISK_AWS_SMARTFOLDERS = 'aws_smartfolders';
	public const DISK_AZ_WORKFLOW = 'az_workflow';
	public const DISK_AZ_FILE_STORAGE = 'az_file_storage';
	public const DISK_LOCAL = 'local';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'wf_params_id_seq', initialValue: 1)]
	#[ORM\Column(name: 'wf_params_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'params', type: 'json', nullable: false)]
	private array $params = [];

	#[ORM\Column(name: 'notification_target', type: 'string', length: 2048, nullable: true)]
	private ?string $notificationTarget;

	#[ORM\Column(name: 'expiration', type: 'integer', nullable: true)]
	private ?int $expiration;

	#[ORM\OneToOne(targetEntity: WFWorkflow::class, inversedBy: 'parameters', cascade: ['persist', 'remove'])]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'wf_workflow_id', nullable: false)]
	private WFWorkflow $workflow;

	#[ORM\Column(type: 'integer')]
	private int $notificationType;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getParams(): ?array
	{
		return $this->params;
	}

	public function setParams(array $params): self
	{
		$this->params = $params;

		return $this;
	}

	public function setId(int $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getNotificationTarget(): ?string
	{
		return $this->notificationTarget;
	}

	public function setNotificationTarget(?string $notificationTarget): self
	{
		$this->notificationTarget = $notificationTarget;

		return $this;
	}

	public function getExpiration(): ?string
	{
		return $this->expiration;
	}

	public function setExpiration(?string $expiration): self
	{
		$this->expiration = $expiration;

		return $this;
	}

	public function getWorkflow(): ?WFWorkflow
	{
		return $this->workflow;
	}

	public function setWorkflow(?WFWorkflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function getNotificationType(): ?int
	{
		return $this->notificationType;
	}

	public function setNotificationType(int $notificationType): self
	{
		$this->notificationType = $notificationType;

		return $this;
	}
}
