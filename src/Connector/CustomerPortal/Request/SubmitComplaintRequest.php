<?php

namespace App\Connector\CustomerPortal\Request;

use App\Connector\CustomerPortal\Dto\FeedbackDto;

class SubmitComplaintRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/projects/%s/quality-assurance/feedback';
	protected string $type = self::TYPE_JSON;

	public function __construct(string $projectId, FeedbackDto $feedbackDto, ?string $sessionId)
	{
		$this->headers['Cookie']       = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri              = sprintf($this->requestUri, $projectId);
		$this->headers['Content-Type'] = 'application/json';
		$this->params                  = (array) $feedbackDto;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
