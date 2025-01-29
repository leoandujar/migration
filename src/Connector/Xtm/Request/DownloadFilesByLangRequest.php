<?php

namespace App\Connector\Xtm\Request;

class DownloadFilesByLangRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects/%s/files/download?fileType=TARGET&targetLanguages=%s';

	public function __construct(string $projectId, string $lang, $authToken)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri               = sprintf($this->requestUri, $projectId, $lang);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
