<?php

namespace App\Connector\CustomerPortal\Dto;

class CustomerDto
{
	public string $id;
	public ?string $name;
	public ?string $parentName;
	public ?array $billingAddress;
	public ?string $billingCountry;
	public ?string $billingProvince;
	public ?array $correspondenceAddress;
	public ?string $correspondenceCountry;
	public ?string $correspondenceProvince;
	public ?string $billingCity;
	public ?string $billingPostalcode;

	public function setId(string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setParentName(?string $parentName): self
	{
		$this->parentName = $parentName;

		return $this;
	}

	public function setBillingAddress(?array $billingAddress): self
	{
		$this->billingAddress = $billingAddress;

		return $this;
	}

	public function setBillingCountry(?string $billingCountry): self
	{
		$this->billingCountry = $billingCountry;

		return $this;
	}

	public function setBillingProvince(?string $billingProvince): self
	{
		$this->billingProvince = $billingProvince;

		return $this;
	}

	public function setCorrespondenceAddress(?array $correspondenceAddress): self
	{
		$this->correspondenceAddress = $correspondenceAddress;

		return $this;
	}

	public function setCorrespondenceCountry(?string $correspondenceCountry): self
	{
		$this->correspondenceCountry = $correspondenceCountry;

		return $this;
	}

	public function setCorrespondenceProvince(?string $correspondenceProvince): self
	{
		$this->correspondenceProvince = $correspondenceProvince;

		return $this;
	}

	public function setBillingCity(?string $billingCity): self
	{
		$this->billingCity = $billingCity;

		return $this;
	}

	public function setBillingPostalcode(?string $billingPostalcode): self
	{
		$this->billingPostalcode = $billingPostalcode;

		return $this;
	}
}
