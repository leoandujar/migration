<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'ap_form_template')]
#[ORM\Index(name: '', columns: ['ap_form_template_id'])]
#[ORM\Entity]
class APFormTemplate implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'ap_form_template_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'ap_form_template_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'name', type: 'string', length: 30, nullable: false)]
	private string $name;

	#[ORM\Column(name: 'type', type: 'integer', nullable: true)]
	private ?int $type;

	#[ORM\Column(name: 'content', type: 'text', nullable: false)]
	private string $content;

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

	/**
	 * @return mixed
	 */
	public function getType(): ?int
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(?int $type): self
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getContent(): ?string
	{
		return $this->content;
	}

	/**
	 * @return mixed
	 */
	public function setContent(string $content): self
	{
		$this->content = $content;

		return $this;
	}
}
