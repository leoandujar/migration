<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_pipeline_stage')]
#[ORM\Index(name: '', columns: ['hs_pipeline_stage_id'])]
#[ORM\Entity]
class HsPipelineStage implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_pipeline_stage_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_pipeline_stage_id', type: 'bigint')]
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

	#[ORM\Column(name: 'metadata', type: 'json', nullable: true)]
	private ?array $metadata;

	#[ORM\ManyToOne(targetEntity: HsPipeline::class)]
	#[ORM\JoinColumn(name: 'hs_pipeline_id', referencedColumnName: 'hs_pipeline_id', nullable: false)]
	private HsPipeline $pipeline;

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
	public function getPipeline(): ?HsPipeline
	{
		return $this->pipeline;
	}

	/**
	 * @return mixed
	 */
	public function setPipeline(?HsPipeline $pipeline): self
	{
		$this->pipeline = $pipeline;

		return $this;
	}
}
