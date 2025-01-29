<?php

namespace App\Connector\Xtrf\Request\Projects;

use App\Connector\Xtrf\Request\Request;

class UploadProjectFileRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/files';
	protected string $type = Request::TYPE_MULTIPART;

	public function __construct(array $file)
	{
		$this->params = $file;
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
