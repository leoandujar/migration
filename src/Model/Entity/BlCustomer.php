<?php

namespace App\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_customer')]
#[ORM\Index(name: '', columns: ['blcustomer_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['blcustomer_id'])]
#[ORM\Entity]
class BlCustomer implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_customer_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_customer_id', type: 'bigint')]
	private string $id;

	#[ORM\OneToOne(targetEntity: Customer::class, inversedBy: 'blCustomer')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: true)]
	private ?Customer $customer;

	#[ORM\Column(name: 'blcustomer_id', type: 'bigint', nullable: false)]
	private string $blCustomerId;

	#[ORM\OneToMany(targetEntity: BlContact::class, mappedBy: 'blCustomer', cascade: ['persist'])]
	private mixed $blContacts;

	#[ORM\Column(name: 'invited_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $invitedDate;

	#[ORM\Column(name: 'accepted_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $acceptedDate;

	#[ORM\Column(name: 'status', type: 'integer', nullable: false)]
	private int $status;

	#[ORM\Column(name: 'user_number', type: 'integer', nullable: false)]
	private int $userNumber;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\OneToMany(targetEntity: BlCall::class, mappedBy: 'blCustomer')]
	private mixed $blCalls;

	public function __construct()
	{
		$this->blContacts = new ArrayCollection();
		$this->blCalls = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlCustomerId(): ?string
	{
		return $this->blCustomerId;
	}

	public function setBlCustomerId(string $blCustomerId): self
	{
		$this->blCustomerId = $blCustomerId;

		return $this;
	}

	public function getInvitedDate(): ?\DateTimeInterface
	{
		return $this->invitedDate;
	}

	public function setInvitedDate(?\DateTimeInterface $invitedDate): self
	{
		$this->invitedDate = $invitedDate;

		return $this;
	}

	public function getAcceptedDate(): ?\DateTimeInterface
	{
		return $this->acceptedDate;
	}

	public function setAcceptedDate(?\DateTimeInterface $acceptedDate): self
	{
		$this->acceptedDate = $acceptedDate;

		return $this;
	}

	public function getStatus(): ?int
	{
		return $this->status;
	}

	public function setStatus(int $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getUserNumber(): ?int
	{
		return $this->userNumber;
	}

	public function setUserNumber(int $userNumber): self
	{
		$this->userNumber = $userNumber;

		return $this;
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

	/**
	 * @return Collection|BlContact[]
	 */
	public function getBlContacts(): Collection
	{
		return $this->blContacts;
	}

	public function addBlContact(BlContact $blContact): self
	{
		if (!$this->blContacts->contains($blContact)) {
			$this->blContacts[] = $blContact;
			$blContact->setBlCustomer($this);
		}

		return $this;
	}

	public function removeBlContact(BlContact $blContact): self
	{
		if ($this->blContacts->removeElement($blContact)) {
			// set the owning side to null (unless already changed)
			if ($blContact->getBlCustomer() === $this) {
				$blContact->setBlCustomer(null);
			}
		}

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): self
	{
		$this->customer = $customer;

		return $this;
	}

	public function getBlCalls(): Collection
	{
		return $this->blCalls;
	}

	/**
	 * @return mixed
	 */
	public function addBlCall(BlCall $blCall): self
	{
		if (!$this->blCalls->contains($blCall)) {
			$this->blCalls[] = $blCall;
			$blCall->setBlCustomer($this);
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function removeBlCall(BlCall $blCall): self
	{
		if ($this->blCalls->contains($blCall)) {
			$this->blCalls->removeElement($blCall);
			// set the owning side to null (unless already changed)
			if ($blCall->getBlCustomer() === $this) {
				$blCall->setBlCustomer(null);
			}
		}

		return $this;
	}
}
