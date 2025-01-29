<?php

namespace App\Model\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cp_token')]
#[ORM\Entity]
class CPToken
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'cp_token_id', type: 'string', length: 36)]
	private string $id;

	#[ORM\Column(type: 'string', length: 10)]
	private string $token;

	#[ORM\Column(type: 'datetime')]
	private ?\DateTimeInterface $createdAt;

	#[ORM\Column(type: 'datetime')]
	private ?\DateTimeInterface $expiresAt;

	#[ORM\ManyToOne(targetEntity: ContactPerson::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'contact_person_id', nullable: true)]
	private ?ContactPerson $user;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function setID($id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getCreatedAt(): ?\DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedAt(\DateTime $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getExpiresAt(): ?\DateTimeInterface
	{
		return $this->expiresAt;
	}

	/**
	 * @return mixed
	 */
	public function setExpiresAt(\DateTimeInterface $expiresAt): self
	{
		$this->expiresAt = $expiresAt;

		return $this;
	}

	public function getUser(): ?ContactPerson
	{
		return $this->user;
	}

	/**
	 * @return mixed
	 */
	public function setUser(?ContactPerson $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function getToken(): ?string
	{
		return $this->token;
	}

	/**
	 * @return mixed
	 */
	public function setToken(string $token): self
	{
		$this->token = $token;

		return $this;
	}
}
