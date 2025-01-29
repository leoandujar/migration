<?php

namespace App\Connector\CustomerPortal\Request;

class UploadFilesTaskReviewRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri    = '/projects/tasks/%s/review/files/reviewed';
	protected string $type          = Request::TYPE_MULTIPART;

	public function __construct(string $taskId, array $files, string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $taskId);
		$this->params            = $files;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
