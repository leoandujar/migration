<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\Collection;

#[ORM\Table(name: 'av_flow_action')]
#[ORM\Entity]
class AvFlowAction
{
	#[ORM\Id]
	#[ORM\Column(type: 'string', length: 36, unique: true)]
	private string $id;

	#[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
	#[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
	private ?self $parent = null;

	#[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
	private Collection $children;

	#[ORM\ManyToOne(targetEntity: AvFlow::class, inversedBy: 'actions')]
	#[ORM\JoinColumn(name: 'flow_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
	private AvFlow $flow;

	#[ORM\Column(name: 'name', type: 'string', length: 80, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'description', type: 'string', length: 200, nullable: true)]
	private ?string $description;

	#[ORM\Column(name: 'action', type: 'string', length: 80, nullable: false)]
	private string $action;

	#[ORM\OneToOne(targetEntity: AvFlowAction::class)]
	#[ORM\JoinColumn(name: 'next_action_id', referencedColumnName: 'id', nullable: true)]
	private ?AvFlowAction $next;

	#[ORM\Column(name: 'category', type: 'string', length: 20, nullable: true)]
	private ?string $category;

	#[ORM\Column(name: 'inputs', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $inputs;

	#[ORM\Column(name: 'slug', type: 'string', length: 80, nullable: true)]
	private ?string $slug = null;

	#[ORM\Column(name: 'inputsOnStart', type: 'json', nullable: true, options: ['jsonb' => true])]
	private ?array $inputsOnStart = null;

	private ?array $outputs = [];

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
		$this->children = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function setId(string $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getSlug(): ?string
	{
		return $this->slug;
	}

	public function setSlug(?string $slug): static
	{
		$this->slug = $slug;

		return $this;
	}

    public function getInputsOnStart(): ?array
    {
        return $this->inputsOnStart;
    }

    public function setInputsOnStart(?array $inputsOnStart): static
    {
        $this->inputsOnStart = $inputsOnStart;

        return $this;
    }

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getAction(): ?string
	{
		return $this->action;
	}

	public function setAction(string $action): static
	{
		$this->action = $action;

		return $this;
	}

	public function getInputs(): ?array
	{
		return $this->inputs;
	}

	public function setInputs(?array $inputs): static
	{
		$this->inputs = $inputs;

		return $this;
	}

	public function getFlow(): ?AvFlow
	{
		return $this->flow;
	}

	public function setFlow(?AvFlow $flow): static
	{
		$this->flow = $flow;

		return $this;
	}

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): static
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * @return Collection<int, AvFlowAction>
	 */
	public function getChildren(): Collection
	{
		return $this->children;
	}

	public function setChildren(Collection $children): static
	{
		$this->children = $children;

		return $this;
	}

	public function addChild(AvFlowAction $child): static
	{
		if (!$this->children->contains($child)) {
			$this->children->add($child);
			$child->setParent($this);
		}

		return $this;
	}

	public function removeChild(AvFlowAction $child): static
	{
		if ($this->children->removeElement($child)) {
			if ($child->getParent() === $this) {
				$child->setParent(null);
			}
		}

		return $this;
	}

	public function getCategory(): string
	{
		return $this->category;
	}

	public function setCategory(string $category): static
	{
		$this->category = $category;

		return $this;
	}

	public function getNext(): ?self
	{
		return $this->next;
	}

	public function setNext(?self $next): static
	{
		$this->next = $next;

		return $this;
	}

	public function getOutputs(): ?array
	{
		return $this->outputs;
	}

	public function setOutputs(?array $outputs): static
	{
		$this->outputs = $outputs;

		return $this;
	}
}
