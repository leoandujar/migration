<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'xtm_lqa_issue_type_mapping')]
#[ORM\UniqueConstraint(name: 'parent_name_idx', columns: ['parent_id', 'name'])]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\LqaIssueTypeMappingRepository')]
class LqaIssueTypeMapping implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'lqa_issue_type_mapping_id', type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'string', length: 255)]
	private string $name;

	#[ORM\Column(type: 'smallint')]
	private int $weight;

	#[ORM\OneToMany(targetEntity: LqaIssueTypeMapping::class, mappedBy: 'parent')]
	private mixed $childs;

	#[ORM\ManyToOne(targetEntity: LqaIssueTypeMapping::class, inversedBy: 'childs')]
	#[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'lqa_issue_type_mapping_id', nullable: true)]
	private ?LqaIssueTypeMapping $parent;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private ?string $path;

	#[ORM\Column(type: 'integer', nullable: true)]
	private int $pathDepth = 0;

	#[ORM\Column(type: 'boolean')]
	private bool $active;

	#[ORM\ManyToOne(targetEntity: LqaIssueType::class)]
	#[ORM\JoinColumn(name: 'lqa_issue_type_id', referencedColumnName: 'lqa_issue_type_id', nullable: true)]
	private ?LqaIssueType $lqaIssueType;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->childs = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getWeight(): ?int
	{
		return $this->weight;
	}

	/**
	 * @return mixed
	 */
	public function setWeight(int $weight): self
	{
		$this->weight = $weight;

		return $this;
	}

	public function getChilds(): Collection
	{
		return $this->childs;
	}

	/**
	 * @return mixed
	 */
	public function addChild(LqaIssueTypeMapping $child): self
	{
		if (!$this->childs->contains($child)) {
			$this->childs[] = $child;
			$child->setParent($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeChild(LqaIssueTypeMapping $child): self
	{
		if ($this->childs->contains($child)) {
			$this->childs->removeElement($child);
			// set the owning side to null (unless already changed)
			if ($child->getParent() === $this) {
				$child->setParent(null);
			}
		}

		return $this;
	}

	public function getParent(): ?self
	{
		return $this->parent;
	}

	/**
	 * @return mixed
	 */
	public function setParent(?self $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	public function getPath(): ?string
	{
		return $this->path;
	}

	/**
	 * @return mixed
	 */
	public function setPath(?string $path): self
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPathDepth(): int
	{
		return $this->pathDepth;
	}

	/**
	 * @return mixed
	 */
	public function setPathDepth(int $pathDepth): self
	{
		$this->pathDepth = $pathDepth;

		return $this;
	}

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

	public function getLqaIssueType(): ?LqaIssueType
	{
		return $this->lqaIssueType;
	}

	/**
	 * @return mixed
	 */
	public function setLqaIssueType(?LqaIssueType $lqaIssueType): self
	{
		$this->lqaIssueType = $lqaIssueType;

		return $this;
	}

	/**
	 *  Generate a hash from attributes.
	 */
	public function hashFromObject(): string
	{
		$parent = ($this->parent instanceof self) ? $this->parent->getId() : null;
		$lqaIssueType = ($this->lqaIssueType instanceof LqaIssueType) ? $this->lqaIssueType->getId() : null;

		return md5("{$this->weight}$parent{$this->active}$lqaIssueType$this->path $this->pathDepth");
	}

	/**
	 *  Generate a hash from attributes in the remote resource.
	 */
	public function hashFromRemote($remoteSource): string
	{
		$parent = ($remoteSource['parent'] instanceof self) ? $remoteSource['parent']->getId() : null;
		$lqaIssueType = ($this->lqaIssueType instanceof LqaIssueType) ? $this->lqaIssueType->getId() : null;

		return md5("{$remoteSource['weight']}$parent{$remoteSource['active']}$lqaIssueType{$remoteSource['path']}{$remoteSource['pathDepth']}");
	}

	/**
	 *  Update the entity with the data in the remote.
	 */
	public function populateFromRemote($remoteSource): void
	{
		$this
			->setWeight($remoteSource['weight'])
			->setParent($remoteSource['parent'])
			->setActive($remoteSource['active'])
			->setLqaIssueType($remoteSource['lqaIssueType'])
			->setPath($remoteSource['path'])
			->setPathDepth($remoteSource['pathDepth']);
	}
}
