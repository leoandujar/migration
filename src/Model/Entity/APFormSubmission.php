<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'ap_form_submission')]
#[ORM\Index(name: '', columns: ['ap_form_submission_id'])]
#[ORM\Entity]
class APFormSubmission implements EntityInterface
{
	public const STATUS_PENDING = 'pending';
	public const STATUS_APPROVED = 'approved';
	public const STATUS_DENIED = 'denied';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'ap_form_submission_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'ap_form_submission_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'status', type: 'string', length: 15, nullable: false)]
	private string $status = self::STATUS_PENDING;

	#[ORM\ManyToOne(targetEntity: APForm::class)]
	#[ORM\JoinColumn(name: 'ap_form_id', referencedColumnName: 'ap_form_id', nullable: false)]
	private APForm $apForm;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'submitted_by', referencedColumnName: 'internal_user_id', nullable: false)]
	private InternalUser $submittedBy;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'approved_by', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $approvedBy;

	#[ORM\Column(name: 'submitted_at', type: 'datetime', nullable: false)]
	private \DateTimeInterface $submittedAt;

	#[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $updatedAt;

	#[ORM\Column(name: 'submitted_data', type: 'json', nullable: false)]
	private array $submittedData;

	#[ORM\Column(name: 'collaborators', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $collaborators;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $owner;

	/**
	 * APFormSubmission constructor.
	 */
	public function __construct()
	{
		$this->submittedAt = new \DateTime('now');
	}

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getStatus(): ?string
	{
		return $this->status;
	}

	/**
	 * @return mixed
	 */
	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSubmittedAt(): ?\DateTimeInterface
	{
		return $this->submittedAt;
	}

	/**
	 * @return mixed
	 */
	public function setSubmittedAt(\DateTimeInterface $submittedAt): self
	{
		$this->submittedAt = $submittedAt;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	/**
	 * @return mixed
	 */
	public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSubmittedData(): ?array
	{
		return $this->submittedData;
	}

	/**
	 * @return mixed
	 */
	public function setSubmittedData(array $submittedData): self
	{
		$this->submittedData = $submittedData;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApForm(): ?APForm
	{
		return $this->apForm;
	}

	/**
	 * @return mixed
	 */
	public function setApForm(?APForm $apForm): self
	{
		$this->apForm = $apForm;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getSubmittedBy(): ?InternalUser
	{
		return $this->submittedBy;
	}

	/**
	 * @return mixed
	 */
	public function setSubmittedBy(?InternalUser $submittedBy): self
	{
		$this->submittedBy = $submittedBy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApprovedBy(): ?InternalUser
	{
		return $this->approvedBy;
	}

	/**
	 * @return mixed
	 */
	public function setApprovedBy(?InternalUser $approvedBy): self
	{
		$this->approvedBy = $approvedBy;

		return $this;
	}

	public function getCollaborators(): ?array
	{
		return $this->collaborators;
	}

	public function setCollaborators(?array $collaborators): self
	{
		$this->collaborators = $collaborators;

		return $this;
	}

	public function getOwner(): ?InternalUser
	{
		return $this->owner;
	}

	public function setOwner(?InternalUser $owner): self
	{
		$this->owner = $owner;

		return $this;
	}
}
