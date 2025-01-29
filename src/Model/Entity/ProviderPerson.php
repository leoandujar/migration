<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'provider_person')]
#[ORM\Entity]
class ProviderPerson implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\OneToOne(targetEntity: ContactPerson::class, inversedBy: 'provider')]
	#[ORM\JoinColumn(name: 'contact_person_id', referencedColumnName: 'contact_person_id', nullable: false)]
	private ContactPerson $id;

	#[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'providerPersons')]
	#[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_id', nullable: true)]
	private ?Provider $provider;

	#[ORM\Column(name: 'preferences_id', type: 'bigint', nullable: true)]
	private ?string $preferences;

	#[ORM\Column(name: 'invitation_sent', type: 'boolean', nullable: false)]
	private bool $invitationSent;

	public function getPreferences(): ?string
	{
		return $this->preferences;
	}

	/**
	 * @return mixed
	 */
	public function setPreferences(?string $preferences): self
	{
		$this->preferences = $preferences;

		return $this;
	}

	public function getId(): ?ContactPerson
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function setId(ContactPerson $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getProvider(): ?Provider
	{
		return $this->provider;
	}

	/**
	 * @return mixed
	 */
	public function setProvider(?Provider $provider): self
	{
		$this->provider = $provider;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getInvitationSent(): ?bool
	{
		return $this->invitationSent;
	}

	/**
	 * @param bool $invitationSent
	 *
	 * @return mixed
	 */
	public function setInvitationSent(?Provider $invitationSent): self
	{
		$this->invitationSent = $invitationSent;

		return $this;
	}
}
