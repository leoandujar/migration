<?php

namespace App\Connector\Xtrf\Response\Customers;

use App\Connector\Xtrf\Dto\AddressDto;
use App\Connector\Xtrf\Dto\ContactDto;
use App\Connector\Xtrf\Dto\CustomerDto;
use App\Connector\Xtrf\Response\Response;

class GetCustomerResponse extends Response
{
	private ?CustomerDto $customerDto;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$address = new AddressDto();
			$correspondenceAddress = new AddressDto();
			$contact = new ContactDto();
			$contactData = $this->raw['contact'] ?? [];
			$addressData = $this->raw['billingAddress'] ?? [];
			$correspondenceAddressData = $this->raw['correspondenceAddress'] ?? [];
			$address->city = $addressData['city'] ?? null;
			$address->postalCode = $addressData['postalCode'] ?? null;
			$address->addressLine1 = $addressData['addressLine1'] ?? null;
			$address->addressLine2 = $addressData['addressLine2'] ?? null;
			$address->countryId = $addressData['countryId'] ?? null;
			$address->provinceId = $addressData['provinceId'] ?? null;
			$address->sameAsBillingAddress = $addressData['sameAsBillingAddress'] ?? null;
			$correspondenceAddress->city = $correspondenceAddressData['city'] ?? null;
			$correspondenceAddress->postalCode = $correspondenceAddressData['postalCode'] ?? null;
			$correspondenceAddress->addressLine1 = $correspondenceAddressData['addressLine1'] ?? null;
			$correspondenceAddress->addressLine2 = $correspondenceAddressData['addressLine2'] ?? null;
			$correspondenceAddress->sameAsBillingAddress = $addressData['sameAsBillingAddress'] ?? null;
			$correspondenceAddress->countryId = $addressData['countryId'] ?? null;
			$correspondenceAddress->provinceId = $addressData['provinceId'] ?? null;
			$id = $this->raw['id'] ?? null;
			$name = $this->raw['name'] ?? null;
			$contact->phones = $contactData['phones'] ?? [];
			$contact->sms = $contactData['sms'] ?? null;
			$contact->fax = $contactData['fax'] ?? null;
			$contact->emails = $contactData['emails'] ?? null;

			$this->customerDto = new CustomerDto($id, $name, $address, $correspondenceAddress, $contact);
		}
	}

	public function getCustomerDto(): ?CustomerDto
	{
		return $this->customerDto;
	}
}
