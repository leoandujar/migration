<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'ap_template')]
#[ORM\Index(columns: ['ap_template_id'])]
#[ORM\Index(columns: ['internal_user_id'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['target_entity'])]
#[ORM\UniqueConstraint(name: '', columns: ['name', 'internal_user_id'])]
#[ORM\Entity]
#[UniqueEntity(fields: ['name', 'internalUser'], message: 'Name already exists under that user.')]
class APTemplate
{
	public const TARGET_ENTITY_PROJECT = 1;
	public const TARGET_ENTITY_QUOTE = 2;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'ap_template_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'ap_template_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: InternalUser::class, inversedBy: 'apTemplates')]
	#[ORM\JoinColumn(name: 'internal_user_id', referencedColumnName: 'internal_user_id', nullable: false)]
	private InternalUser $internalUser;

	#[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'target_entity', type: 'integer', nullable: false, options: ['default' => '1'])]
	private int $targetEntity = self::TARGET_ENTITY_PROJECT;

	#[ORM\Column(name: 'data', type: 'json', nullable: false)]
	private array $data;

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

	public function getTargetEntity(): ?int
	{
		return $this->targetEntity;
	}

	public function setTargetEntity(int $targetEntity): self
	{
		$this->targetEntity = $targetEntity;

		return $this;
	}

	public function getData(): ?array
	{
		return $this->data;
	}

	public function setData(array $data): self
	{
		$this->data = $data;

		return $this;
	}

	public function getInternalUser(): ?InternalUser
	{
		return $this->internalUser;
	}

	public function setInternalUser(?InternalUser $internalUser): self
	{
		$this->internalUser = $internalUser;

		return $this;
	}
}
