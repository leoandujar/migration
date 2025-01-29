<?php

namespace App\Connector\XtrfMacro\Request;

class MacroStatusRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/asynch/%s';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $actionId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri = sprintf($this->requestUri, $actionId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
