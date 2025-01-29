<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_contact')]
#[ORM\Entity]
class BlContact implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_contact_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_contact_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blcontact_id', type: 'bigint', nullable: false)]
	private string $blContactId;

	#[ORM\ManyToOne(targetEntity: BlCustomer::class, inversedBy: 'blContacts')]
	#[ORM\JoinColumn(name: 'bl_customer_id', referencedColumnName: 'bl_customer_id', nullable: true)]
	private ?BlCustomer $blCustomer;

	#[ORM\Column(name: 'invitation_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $invitationDate;

	#[ORM\Column(name: 'pin', type: 'string', nullable: true)]
	private ?string $pin;

	#[ORM\Column(name: 'email', type: 'string', nullable: true)]
	private ?string $email;

	#[ORM\Column(name: 'phone', type: 'string', nullable: true)]
	private ?string $phone;

	#[ORM\Column(name: 'name', type: 'string', nullable: true)]
	private ?string $name;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlContactId(): ?string
	{
		return $this->blContactId;
	}

	public function setBlContactId(string $blContactId): self
	{
		$this->blContactId = $blContactId;

		return $this;
	}

	public function getInvitationDate(): ?\DateTimeInterface
	{
		return $this->invitationDate;
	}

	public function setInvitationDate(?\DateTimeInterface $invitationDate): self
	{
		$this->invitationDate = $invitationDate;

		return $this;
	}

	public function getPin(): ?string
	{
		return $this->pin;
	}

	public function setPin(?string $pin): self
	{
		$this->pin = $pin;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): self
	{
		$this->phone = $phone;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getBlCustomer(): ?BlCustomer
	{
		return $this->blCustomer;
	}

	public function setBlCustomer(?BlCustomer $blCustomer): self
	{
		$this->blCustomer = $blCustomer;

		return $this;
	}
}
