<?php

namespace App\Connector\XtrfMacro\Request;

class MacroRunRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/macros/%s/run';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $macroId, array $data)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->params = $data;
		$this->requestUri = sprintf($this->requestUri, $macroId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
