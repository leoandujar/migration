<?php

namespace App\Connector\Xtrf\Request\Subscriptions;

use App\Connector\Xtrf\Request\Request;

class SubscriptionDeleteRequest extends Request
{
	protected string $requestMethod = 'DELETE';
	protected string $requestUri = '/subscription/%s';

	public function __construct(string $subscriptionId)
	{
		$this->requestUri              = sprintf($this->requestUri, $subscriptionId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
