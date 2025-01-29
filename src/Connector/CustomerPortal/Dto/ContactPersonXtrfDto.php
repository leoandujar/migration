<?php

namespace App\Connector\CustomerPortal\Dto;

class ContactPersonXtrfDto
{
	public string $id;
	public int $version;
	public string $name;
	public string $email;
	public string $position;
	public string $firstName;
	public string $lastName;
	public bool $usePartnerAddress;
	public array $address = [
		'country'    => [
			'id'            => null,
			'name'          => null,
			'localizedName' => null,
			'symbol'        => null,
		],
		'province'   => [
			'id'            => null,
			'name'          => null,
			'localizedName' => null,
		],
		'city'       => null,
		'postalCode' => null,
		'address'    => null,
		'address2'   => null,
	];
	public array $contact = [
		'phones'              => [],
		'mobile'              => null,
		'smsEnabled'          => null,
		'fax'                 => null,
		'email'               => null,
		'www'                 => null,
		'socialMediaContacts' => [],
	];

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function setPosition(string $position): self
	{
		$this->position = $position;

		return $this;
	}

	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function setUsePartnerAddress(bool $usePartnerAddress): self
	{
		$this->usePartnerAddress = $usePartnerAddress;

		return $this;
	}

	public function setAddress(array $address): self
	{
		$this->address = $address;

		return $this;
	}

	public function setContact(array $contact): self
	{
		$this->contact = $contact;

		return $this;
	}
}
