<?php

namespace App\Connector\Xtrf\Request\Invoices;

use App\Connector\Xtrf\Request\Request;

class CreateInvoicePaymentRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/accounting/customers/invoices/%s/payments';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $invoiceId, array $params)
	{
		$this->requestUri              = sprintf($this->requestUri, $invoiceId);
		$this->params                  = $params;
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
