<?php

namespace App\Connector\CustomerPortal\Request;

class ProjectConfirmationFileRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $type          = self::TYPE_JSON;
	protected string $requestUri    = '/projects/%s/confirmation';

	public function __construct(string $projectId, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $projectId);
		$this->headers['Accept'] = 'application/*';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
