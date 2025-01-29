<?php

namespace App\Connector\Xtm\Request;

class ProjectFileRequest extends Request
{
	public const FILE_TYPE_LQA_REPORT = 'LQA_REPORT';
	public const FILE_TYPE_EXCEL_EXTENDED_TABLE = 'EXCEL_EXTENDED_TABLE';

	protected string $requestMethod = 'POST';
	protected string $requestUri = '/projects/%s/files/generate';

	public function __construct(string $projectId, array $queryParams, array $params, $authToken)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri = sprintf($this->requestUri, $projectId);
		$this->requestUri .= '?'.http_build_query($queryParams);
		if ($params) {
			$this->params = $params;
		}
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
