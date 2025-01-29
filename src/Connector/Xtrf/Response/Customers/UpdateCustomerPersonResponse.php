<?php

namespace App\Connector\Xtrf\Response\Customers;

use App\Connector\Xtrf\Response\Response;
use App\Connector\Xtrf\Dto\CustomerPersonDto;

class UpdateCustomerPersonResponse extends Response
{
	private CustomerPersonDto $customerPerson;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	public function getCustomerPerson(): CustomerPersonDto
	{
		return $this->customerPerson;
	}
}
