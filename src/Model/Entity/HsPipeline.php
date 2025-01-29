<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'hs_pipeline')]
#[ORM\Index(name: '', columns: ['hs_pipeline_id'])]
#[ORM\Entity]
class HsPipeline implements EntityInterface
{
	public const TYPE_DEAL = 'deals';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_pipeline_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_pipeline_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hs_id', type: 'string', nullable: false)]
	private string $hsId;

	#[ORM\Column(name: 'label', type: 'string', length: 70, nullable: true)]
	private ?string $label;

	#[ORM\Column(name: 'display_order', type: 'integer', nullable: true)]
	private ?int $displayOrder;

	#[ORM\Column(name: 'archived', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $archived;

	#[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $updatedAt;

	#[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdAt;

	#[ORM\OneToMany(targetEntity: HsPipelineStage::class, mappedBy: 'pipeline', cascade: ['persist', 'remove'])]
	private mixed $stages;

	public function __construct()
	{
		$this->stages = new ArrayCollection();
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
	public function getLabel(): ?string
	{
		return $this->label;
	}

	/**
	 * @return mixed
	 */
	public function setLabel(?string $label): self
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayOrder(): ?int
	{
		return $this->displayOrder;
	}

	/**
	 * @return mixed
	 */
	public function setDisplayOrder(?int $displayOrder): self
	{
		$this->displayOrder = $displayOrder;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getArchived(): ?bool
	{
		return $this->archived;
	}

	/**
	 * @return mixed
	 */
	public function setArchived(bool $archived): self
	{
		$this->archived = $archived;

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
	 * @return Collection|HsPipelineStage[]
	 */
	public function getStages(): Collection
	{
		return $this->stages;
	}

	/**
	 * @return mixed
	 */
	public function addStage(HsPipelineStage $stage): self
	{
		if (!$this->stages->contains($stage)) {
			$this->stages[] = $stage;
			$stage->setPipeline($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeStage(HsPipelineStage $stage): self
	{
		if ($this->stages->removeElement($stage)) {
			// set the owning side to null (unless already changed)
			if ($stage->getPipeline() === $this) {
				$stage->setPipeline(null);
			}
		}

		return $this;
	}
}
