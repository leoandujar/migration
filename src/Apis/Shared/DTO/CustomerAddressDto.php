<?php

namespace App\Apis\Shared\DTO;

class CustomerAddressDto
{
	public function __construct(
		public ?string $addressAddress,
		public ?string $addressCity,
		public ?string $addressProvince,
		public ?string $addressCountry,
		public ?string $addressZipCode,
		public ?string $addressAddress2,
		public ?string $correspondenceAddress,
		public ?string $correspondenceCity,
		public ?string $correspondenceProvince,
		public ?string $correspondenceCountry,
		public ?string $correspondenceZipCode,
		public ?string $correspondenceAddress2,
		public ?bool $useAddressAsCorrespondence,
		public ?string $phone,
		public ?string $addressPhone2,
		public ?string $addressPhone3,
		public ?string $fax,
		public ?string $email,
		public ?string $www
	) {
	}
}
