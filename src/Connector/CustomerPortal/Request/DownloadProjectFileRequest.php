<?php

namespace App\Connector\CustomerPortal\Request;

class DownloadProjectFileRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/system/session/files/%s';

	public function __construct(string $fileId, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $fileId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
