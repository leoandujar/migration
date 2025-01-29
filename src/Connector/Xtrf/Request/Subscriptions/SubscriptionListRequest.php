<?php

namespace App\Connector\Xtrf\Request\Subscriptions;

use App\Connector\Xtrf\Request\Request;

class SubscriptionListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/subscription';

	public function __construct()
	{
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
