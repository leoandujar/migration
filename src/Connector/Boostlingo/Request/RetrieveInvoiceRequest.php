<?php

namespace App\Connector\Boostlingo\Request;

class RetrieveInvoiceRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/invoice/invoice/interpreter/%s';

	public function __construct(int $invoiceId, string $token)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Authorization'] = "Bearer $token";
		$this->requestUri = sprintf($this->requestUri, $invoiceId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
