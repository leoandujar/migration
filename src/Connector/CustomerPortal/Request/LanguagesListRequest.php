<?php

namespace App\Connector\CustomerPortal\Request;

class LanguagesListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/system/values/languages';

	public function __construct(?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
