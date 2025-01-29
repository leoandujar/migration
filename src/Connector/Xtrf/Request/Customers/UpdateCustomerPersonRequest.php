<?php

namespace App\Connector\Xtrf\Request\Customers;

use App\Connector\Xtrf\Request\Request;
use App\Connector\Xtrf\Dto\CustomerPersonDto;

class UpdateCustomerPersonRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri = '/customers/persons/%s';
	protected string $type = Request::TYPE_JSON;

	public function __construct(CustomerPersonDto $customerPersonDto)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $customerPersonDto->id);
		$this->params                  = (array) $customerPersonDto;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
