<?php

namespace App\Connector\CustomerPortal\Request;

class QuoteAcceptDeclineRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/quotes/%s';

	public function __construct(string $quoteId, array $params, ?string $sessionId)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri              = sprintf($this->requestUri, $quoteId);
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
		$this->params                  = $params;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
