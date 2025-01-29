<?php

namespace App\Connector\CustomerPortal\Request;

use App\Connector\CustomerPortal\Dto\ContactPersonXtrfDto;

class UpdateContactPersonRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri    = '/customers/%s/persons/%s';
	protected string $type          = Request::TYPE_JSON;

	public function __construct(string $customerId, ContactPersonXtrfDto $contactPersonXtrfDto, ?string $sessionId)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $customerId, $contactPersonXtrfDto->id);
		$this->params                  = (array) $contactPersonXtrfDto;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
