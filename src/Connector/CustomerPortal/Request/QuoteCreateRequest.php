<?php

namespace App\Connector\CustomerPortal\Request;

class QuoteCreateRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/v2/quotes';
	protected string $type = self::TYPE_JSON;

	public function __construct(array $params, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->params = $params;
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
