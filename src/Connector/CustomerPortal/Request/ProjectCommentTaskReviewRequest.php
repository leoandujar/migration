<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectCommentTaskReviewRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri    = '/projects/tasks/%s/review?comment=%s';
	protected string $type          = Request::TYPE_JSON;

	public function __construct(string $taskId, string $comment, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $taskId, $comment);
		$this->headers['Accept'] = '*/*';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
