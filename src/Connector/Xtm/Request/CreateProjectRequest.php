<?php

namespace App\Connector\Xtm\Request;

class CreateProjectRequest extends Request
{
	protected string $requestMethod = 'POST';

	protected string $requestUri = '/projects';

	protected string $type = Request::TYPE_MULTIPART;

	/**
	 * @param array $params
	 */
	public function __construct(string $authToken, $params)
	{
		$this->headers['Authorization'] = sprintf('XTM-Basic %s', $authToken);
		$this->params                   = $params;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
