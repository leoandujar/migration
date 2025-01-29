<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'category_supported_classes')]
#[ORM\Entity]
class CategorySupportedClasses implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Category::class)]
	#[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'category_id', nullable: false)]
	private Category $category;

	#[ORM\Id]
	#[ORM\Column(name: 'supported_class', type: 'string', nullable: false)]
	private string $supportedClass;

	public function getSupportedClass(): ?string
	{
		return $this->supportedClass;
	}

	public function getCategory(): ?Category
	{
		return $this->category;
	}

	public function setCategory(?Category $category): static
	{
		$this->category = $category;

		return $this;
	}
}
