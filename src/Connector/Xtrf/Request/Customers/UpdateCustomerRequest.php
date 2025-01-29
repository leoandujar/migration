<?php

namespace App\Connector\Xtrf\Request\Customers;

use App\Connector\Xtrf\Dto\CustomerDto;
use App\Connector\Xtrf\Request\Request;

class UpdateCustomerRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri    = '/customers/%s';
	protected string $type          = Request::TYPE_JSON;

	public function __construct(CustomerDto $customerDto)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $customerDto->id);
		$this->params                  = (array) $customerDto;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
