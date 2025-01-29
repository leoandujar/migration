<?php

namespace App\Connector\CustomerPortal\Request;

class GetProjectFeedbackRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/%s/feedback';
	protected string $type = self::TYPE_JSON;

	public function __construct(string $projectId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $projectId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
