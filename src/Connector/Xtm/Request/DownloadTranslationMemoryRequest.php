<?php

namespace App\Connector\Xtm\Request;

class DownloadTranslationMemoryRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/translation-memory/files/%s/download';
	protected string $type = Request::TYPE_MULTIPART;

	public function __construct(string $authToken, string $fileID)
	{
		$this->headers['Content-Type']  = 'application/octet-stream';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		parent::__construct($this->requestMethod, sprintf($this->requestUri, $fileID), $this->params, $this->headers);
	}
}
