<?php

namespace App\Connector\CustomerPortal\Request;

use App\Model\Entity\Project;

class GetProjectsReviewRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/projects/review';
	protected string $type          = self::TYPE_JSON;

	public function __construct(?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->params            = [
			'status' => [Project::STATUS_OPEN],
			'start'  => 0,
			'limit'  => 100,
		];
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
