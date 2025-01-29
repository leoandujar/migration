<?php

namespace App\Connector\Boostlingo\Request;

class AppointmentDictionariesRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/dictionary/appointment-dictionaries';

	public function __construct(string $token)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Authorization'] = "Bearer $token";
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
