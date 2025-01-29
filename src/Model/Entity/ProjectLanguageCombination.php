<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'project_language_combination')]
#[ORM\UniqueConstraint(name: 'project_unique_combination', columns: ['project_id', 'source_language_id', 'target_language_id'])]
#[ORM\Entity]
class ProjectLanguageCombination implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'project_language_combination_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'project_language_combination_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private int $version;

	#[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'languagesCombinations')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'project_id', nullable: false)]
	private Project $project;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'source_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $sourceLanguage;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'target_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $targetLanguage;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	/**
	 * @return mixed
	 */
	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	/**
	 * @return mixed
	 */
	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getProject(): ?Project
	{
		return $this->project;
	}

	/**
	 * @return mixed
	 */
	public function setProject(?Project $project): self
	{
		$this->project = $project;

		return $this;
	}

	public function getSourceLanguage(): ?XtrfLanguage
	{
		return $this->sourceLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setSourceLanguage(?XtrfLanguage $sourceLanguage): self
	{
		$this->sourceLanguage = $sourceLanguage;

		return $this;
	}

	public function getTargetLanguage(): ?XtrfLanguage
	{
		return $this->targetLanguage;
	}

	/**
	 * @return mixed
	 */
	public function setTargetLanguage(?XtrfLanguage $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}
}
