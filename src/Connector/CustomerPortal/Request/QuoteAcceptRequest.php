<?php

namespace App\Connector\CustomerPortal\Request;

class QuoteAcceptRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $type = self::TYPE_JSON;
	protected string $requestUri = '/quotes/%s/acceptance';

	public function __construct(string $quoteId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri = sprintf($this->requestUri, $quoteId);
		$this->headers['Accept'] = 'application/*';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
