<?php

namespace App\Connector\CustomerPortal\Dto;

class ContactPersonDto
{
	public ?string $firstName;
	public ?string $lastName;
	public ?string $position;
	public ?string $primaryEmail;
	public ?string $phones;
	public ?string $mobile;
	public ?string $fax;
	public ?string $department;
	public ?string $profilePicData;
	public ?string $twoFactorEnabled;

	public function setFirstName(mixed $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function setLastName(mixed $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function setPosition(mixed $position): self
	{
		$this->position = $position;

		return $this;
	}

	public function setPrimaryEmail(mixed $primaryEmail): self
	{
		$this->primaryEmail = $primaryEmail;

		return $this;
	}

	public function setPhones(mixed $phones): self
	{
		$this->phones = $phones;

		return $this;
	}

	public function setMobile(mixed $mobile): self
	{
		$this->mobile = $mobile;

		return $this;
	}

	public function setFax(mixed $fax): self
	{
		$this->fax = $fax;

		return $this;
	}

	public function setDepartment(?string $department): self
	{
		$this->department = $department;

		return $this;
	}

	public function setTwoFactorEnabled($twoFactorEnabled): self
	{
		$this->twoFactorEnabled = $twoFactorEnabled;

		return $this;
	}

	public function setProfilePicData(?string $profilePicData): self
	{
		$this->profilePicData = $profilePicData;

		return $this;
	}
}
