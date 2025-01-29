<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectDownloadFileByIdRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/files/%s';

	public function __construct(string $fileId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $fileId);
		$this->headers['Accept'] = 'application/octet-stream';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
