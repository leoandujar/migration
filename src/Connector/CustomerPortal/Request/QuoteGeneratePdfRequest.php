<?php

namespace App\Connector\CustomerPortal\Request;

class QuoteGeneratePdfRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/invoices/%s/document';

	public function __construct(string $quoteId, ?string $sessionId)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri              = sprintf($this->requestUri, $quoteId);
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Accept']       = 'application/pdf';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
