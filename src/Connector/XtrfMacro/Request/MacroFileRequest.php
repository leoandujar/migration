<?php

namespace App\Connector\XtrfMacro\Request;

class MacroFileRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/files/test_api_macro.txt?token=%s';
	protected string $type = Request::TYPE_MULTIPART;

	public function __construct(string $token)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri = sprintf($this->requestUri, $token);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
