<?php

namespace App\Connector\JsReport\Request;

class RenderReportRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/report';
	protected string $type = Request::TYPE_JSON;

	public function __construct(array $params)
	{
		$this->params = $params;
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
