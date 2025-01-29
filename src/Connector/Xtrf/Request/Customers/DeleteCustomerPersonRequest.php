<?php

namespace App\Connector\Xtrf\Request\Customers;

use App\Connector\Xtrf\Request\Request;

class DeleteCustomerPersonRequest extends Request
{
	protected string $requestMethod = 'DELETE';
	protected string $requestUri = '/customers/persons/%s';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $personId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $personId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
