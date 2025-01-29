<?php

namespace App\Connector\CustomerPortal\Response;

use App\Connector\CustomerPortal\Dto\ContactPersonXtrfDto;

class GetContactPersonResponse extends Response
{
	private ContactPersonXtrfDto $contactPerson;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		$this->contactPerson = new ContactPersonXtrfDto();
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$this->contactPerson
				->setId($this->raw['id'])
				->setVersion($this->raw['version'])
				->setName($this->raw['name'])
				->setEmail($this->raw['email'])
				->setPosition($this->raw['position'])
				->setFirstName($this->raw['firstName'])
				->setLastName($this->raw['lastName'])
				->setUsePartnerAddress($this->raw['usePartnerAddress'])
				->setAddress($this->raw['address'])
				->setContact($this->raw['contact']);
		}
	}

	public function getContactPerson(): ContactPersonXtrfDto
	{
		return $this->contactPerson;
	}
}
