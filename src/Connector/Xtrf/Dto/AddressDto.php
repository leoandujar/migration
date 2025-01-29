<?php

namespace App\Connector\Xtrf\Dto;

class AddressDto
{
	public ?string $countryId;
	public ?string $provinceId;
	public ?string $city;
	public ?string $postalCode;
	public ?string $addressLine1;
	public ?string $addressLine2;
	public ?bool $sameAsBillingAddress;
}
