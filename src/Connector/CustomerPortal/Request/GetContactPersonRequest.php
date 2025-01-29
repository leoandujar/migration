<?php

namespace App\Connector\CustomerPortal\Request;

class GetContactPersonRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/customers/%s/persons/%s';

	public function __construct(string $customerId, string $personId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $customerId, $personId);
		$this->params            = [
			'customerId' => $customerId,
			'personId'   => $personId,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
