<?php

namespace App\Connector\CustomerPortal\Request;

class GetFilesTasksReviewRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/tasks/%s/review/files/reviewed';
	protected string $type = self::TYPE_JSON;

	public function __construct(string $taskId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $taskId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
