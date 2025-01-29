<?php

namespace App\Connector\CustomerPortal\Request;

class DeleteProjectFileRequest extends Request
{
	protected string $requestMethod = 'DELETE';
	protected string $requestUri    = '/system/session/files/%s';

	public function __construct(string $fileId, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $fileId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
