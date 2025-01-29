<?php

namespace App\Connector\CustomerPortal\Request;

class ServicesListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/customers/%s/services';

	public function __construct(string $customerId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri = sprintf($this->requestUri, $customerId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
