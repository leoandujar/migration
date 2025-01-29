<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectSubmitFeedbackRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/projects/%s/feedback';
	protected string $type          = Request::TYPE_JSON;

	public function __construct(string $projectId, array $params, ?string $sessionId)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri              = sprintf($this->requestUri, $projectId);
		$this->params                  = $params;
		$this->headers['Accept']       = '*/*';
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
