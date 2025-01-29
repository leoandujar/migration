<?php

namespace App\Connector\CustomerPortal\Request;

class DeleteFilesTaskReviewRequest extends Request
{
	protected string $requestMethod = 'DELETE';
	protected string $requestUri = '/projects/tasks/%s/review/files/reviewed/%s';

	public function __construct(string $taskId, string $fileName, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri = sprintf($this->requestUri, $taskId, $fileName);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
