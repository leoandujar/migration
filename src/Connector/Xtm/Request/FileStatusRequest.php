<?php

namespace App\Connector\Xtm\Request;

class FileStatusRequest extends Request
{
	public const FILE_STATUS_ERROR = 'ERROR';
	public const FILE_STATUS_IN_PROGRESS = 'IN_PROGRESS';
	public const FILE_STATUS_FINISHED = 'FINISHED';

	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects/%s/files/status';

	public function __construct(string $projectId, array $params, $authToken)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri               = sprintf($this->requestUri, $projectId);
		$this->requestUri .= '?'.http_build_query($params);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
