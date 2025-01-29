<?php

namespace App\Connector\Xtm\Request;

class FileDownloadRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects/%s/files/%s/download';

	public function __construct(string $projectId, string $fileId, array $params, $authToken)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri               = sprintf($this->requestUri, $projectId, intval($fileId));
		$this->requestUri .= '?'.http_build_query($params);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
