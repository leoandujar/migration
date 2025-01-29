<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectDownloadOutFileRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/%s/files/outputFilesAsZip';

	public function __construct(string $projectId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $projectId);
		$this->headers['Accept'] = 'application/octet-stream';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
