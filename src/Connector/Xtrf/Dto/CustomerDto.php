<?php

namespace App\Connector\Xtrf\Dto;

class CustomerDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?AddressDto $billingAddress,
		public ?AddressDto $correspondenceAddress,
		public ?ContactDto $contact
	) {
	}
}
