<?php

namespace App\Connector\Xtm\Request;

class ProjectsByCriteriaRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/projects';

	public function __construct(array $params, $authToken)
	{
		$this->headers['Content-Type']  = 'application/json';
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->requestUri .= '?'.http_build_query($params);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
