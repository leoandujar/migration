<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'hs_engagement')]
#[ORM\Index(name: '', columns: ['hs_engagement_id'])]
#[ORM\Entity]
class HsEngagement implements EntityInterface
{
	public const TYPE_NOTE    = 'NOTE';
	public const TYPE_EMAIL   = 'EMAIL';
	public const TYPE_TASK    = 'TASK';
	public const TYPE_MEETING = 'MEETING';
	public const TYPE_CALL    = 'CALL';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_engagement_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_engagement_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hs_id', type: 'string', nullable: false)]
	private string $hsId;

	#[ORM\Column(name: 'portal_id', type: 'string', length: 70, nullable: true)]
	private ?string $portalId;

	#[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $active;

	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdAt;

	#[ORM\Column(name: 'last_updated', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastUpdated;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $createdBy;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'modified_by', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $modifiedBy;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'hsEngagement')]
	#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $owner;

	#[ORM\Column(name: 'type', type: 'string', length: 70, nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'timestamp', type: 'bigint', nullable: true)]
	private ?string $timestamp;

	#[ORM\Column(name: 'attachments', type: 'json', nullable: true)]
	private ?array $attachments;

	#[ORM\Column(name: 'metadata', type: 'json', nullable: true)]
	private ?array $metadata;

	#[ORM\Column(name: 'scheduled_tasks', type: 'json', nullable: true)]
	private ?array $scheduledTasks;

	#[ORM\OneToMany(targetEntity: HsEngagementAssoc::class, mappedBy: 'hsEngagement', cascade: ['persist', 'remove'])]
	private mixed $associations;

	public function __construct()
	{
		$this->associations = new ArrayCollection();
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
	public function getHsId(): ?string
	{
		return $this->hsId;
	}

	/**
	 * @return mixed
	 */
	public function setHsId(string $hsId): self
	{
		$this->hsId = $hsId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPortalId(): ?string
	{
		return $this->portalId;
	}

	/**
	 * @return mixed
	 */
	public function setPortalId(?string $portalId): self
	{
		$this->portalId = $portalId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getActive(): ?bool
	{
		return $this->active;
	}

	/**
	 * @return mixed
	 */
	public function setActive(bool $active): self
	{
		$this->active = $active;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedAt(?\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLastUpdated(): ?\DateTimeInterface
	{
		return $this->lastUpdated;
	}

	/**
	 * @return mixed
	 */
	public function setLastUpdated(?\DateTimeInterface $lastUpdated): self
	{
		$this->lastUpdated = $lastUpdated;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttachments(): ?array
	{
		return $this->attachments;
	}

	/**
	 * @return mixed
	 */
	public function setAttachments(?array $attachments): self
	{
		$this->attachments = $attachments;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMetadata(): ?array
	{
		return $this->metadata;
	}

	/**
	 * @return mixed
	 */
	public function setMetadata(?array $metadata): self
	{
		$this->metadata = $metadata;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedBy(): ?InternalUser
	{
		return $this->createdBy;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedBy(?InternalUser $createdBy): self
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getModifiedBy(): ?InternalUser
	{
		return $this->modifiedBy;
	}

	/**
	 * @return mixed
	 */
	public function setModifiedBy(?InternalUser $modifiedBy): self
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOwner(): ?InternalUser
	{
		return $this->owner;
	}

	/**
	 * @return mixed
	 */
	public function setOwner(?InternalUser $owner): self
	{
		$this->owner = $owner;

		return $this;
	}

	/**
	 * @return Collection|HsEngagementAssoc[]
	 */
	public function getAssociations(): Collection
	{
		return $this->associations;
	}

	/**
	 * @return mixed
	 */
	public function addAssociation(HsEngagementAssoc $association): self
	{
		if (!$this->associations->contains($association)) {
			$this->associations[] = $association;
			$association->setHsEngagement($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeAssociation(HsEngagementAssoc $association): self
	{
		if ($this->associations->removeElement($association)) {
			// set the owning side to null (unless already changed)
			if ($association->getHsEngagement() === $this) {
				$association->setHsEngagement(null);
			}
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getScheduledTasks(): ?array
	{
		return $this->scheduledTasks;
	}

	/**
	 * @return mixed
	 */
	public function setScheduledTasks(?array $scheduledTasks): self
	{
		$this->scheduledTasks = $scheduledTasks;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTimestamp(): ?string
	{
		return $this->timestamp;
	}

	/**
	 * @return mixed
	 */
	public function setTimestamp(?string $timestamp): self
	{
		$this->timestamp = $timestamp;

		return $this;
	}
}
