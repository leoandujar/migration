<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectDownloadTaskReviewFileRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/tasks/%s/review/files/awaiting-review';

	public function __construct(string $taskId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $taskId);
		$this->headers['Accept'] = 'application/octet-stream';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
