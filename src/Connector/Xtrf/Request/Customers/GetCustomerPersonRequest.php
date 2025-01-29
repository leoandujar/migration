<?php

namespace App\Connector\Xtrf\Request\Customers;

use App\Connector\Xtrf\Request\Request;

class GetCustomerPersonRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/customers/persons/%s';

	public function __construct(string $personId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $personId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
