<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'av_parameter')]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['scope'])]
#[ORM\UniqueConstraint(name: '', columns: ['name', 'scope'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['name', 'scope'], message: 'Name already used for that scope.')]
class AVParameter
{
	public const TYPE_SYNC_FUNCTION_DB = 'sync_db_func';
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'parameter_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'parameter_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'scope', type: 'string', length: 150, nullable: false)]
	private string $scope;

	#[ORM\Column(name: 'value', type: 'text', nullable: false)]
	private string $value;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getScope(): ?string
	{
		return $this->scope;
	}

	public function setScope(string $scope): self
	{
		$this->scope = $scope;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(string $value): self
	{
		$this->value = $value;

		return $this;
	}
}
