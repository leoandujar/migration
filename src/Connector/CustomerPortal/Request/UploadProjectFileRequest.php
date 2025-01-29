<?php

namespace App\Connector\CustomerPortal\Request;

class UploadProjectFileRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/system/session/files';
	protected string $type          = Request::TYPE_MULTIPART;

	public function __construct(array $files, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->params            = $files;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
