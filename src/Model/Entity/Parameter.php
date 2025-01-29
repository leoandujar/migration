<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'parameter')]
#[ORM\Entity]
class Parameter implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'parameter_id', type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'string', length: 255)]
	private string $name;

	#[ORM\Column(type: 'string', length: 255)]
	private string $scope;

	#[ORM\Column(type: 'string', length: 3000, nullable: true)]
	private ?string $value;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
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

	public function getScope(): ?string
	{
		return $this->scope;
	}

	/**
	 * @return mixed
	 */
	public function setScope(string $scope): self
	{
		$this->scope = $scope;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function setValue(?string $value): self
	{
		$this->value = $value;

		return $this;
	}
}
