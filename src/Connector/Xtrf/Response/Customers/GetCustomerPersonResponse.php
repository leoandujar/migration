<?php

namespace App\Connector\Xtrf\Response\Customers;

use App\Connector\Xtrf\Response\Response;
use App\Connector\Xtrf\Dto\PersonContactDto;
use App\Connector\Xtrf\Dto\CustomerPersonDto;
use App\Connector\Xtrf\Dto\PersonContactEmailDto;

class GetCustomerPersonResponse extends Response
{
	private CustomerPersonDto $contactPerson;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		$this->contactPerson = new CustomerPersonDto();
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$personContactEmailDto = new PersonContactEmailDto();
			$personContactEmailDto
				->setPrimary($this->raw['contact']['emails']['primary'])
				->setAdditional($this->raw['contact']['emails']['additional']);
			$personContactDto = new PersonContactDto();
			$personContactDto
				->setPhones($this->raw['contact']['phones'])
				->setSms($this->raw['contact']['sms'])
				->setFax($this->raw['contact']['fax'])
				->setEmails($personContactEmailDto);

			$this->contactPerson
				->setId($this->raw['id'])
				->setName($this->raw['name'])
				->setLastName($this->raw['lastName'])
				->setPositionId($this->raw['positionId'])
				->setActive($this->raw['active'])
				->setContact($personContactDto);
		}
	}

	public function getContactPerson(): CustomerPersonDto
	{
		return $this->contactPerson;
	}
}
