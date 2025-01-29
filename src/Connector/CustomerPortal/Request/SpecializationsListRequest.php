<?php

namespace App\Connector\CustomerPortal\Request;

class SpecializationsListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/system/values/specializations';

	public function __construct(?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
