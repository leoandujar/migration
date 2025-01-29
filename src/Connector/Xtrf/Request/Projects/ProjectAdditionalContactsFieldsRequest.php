<?php

namespace App\Connector\Xtrf\Request\Projects;

use App\Connector\Xtrf\Request\Request;

class ProjectAdditionalContactsFieldsRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri = '/projects/%s/contacts';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $projectId, array $params)
	{
		$this->params                  = $params;
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $projectId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
