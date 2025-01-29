<?php

namespace App\Connector\Xtrf\Request\Subscriptions;

use App\Connector\Xtrf\Request\Request;

class SubscriptionCreateRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/subscription';
	protected string $type = Request::TYPE_JSON;

	public function __construct(array $params)
	{
		$this->params     = $params;
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
