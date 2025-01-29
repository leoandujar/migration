<?php

namespace App\Connector\Xtrf\Request\Projects;

use App\Connector\Xtrf\Request\Request;

class UploadTaskFileRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/tasks/%s/files/input';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $taskId, array $data)
	{
		$this->params                  = $data;
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri              = sprintf($this->requestUri, $taskId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
