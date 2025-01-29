<?php

namespace App\Connector\Hubspot\Request\MarketingEmail;

use App\Connector\Hubspot\Request\Request;

class GetMarketingEmailListRequest extends Request
{
	protected string $requestMethod = 'GET';

	protected string $requestUri = '/marketing-emails/v1/emails/with-statistics?limit=%s&offset=%s';

	public function __construct($limit, $offset)
	{
		$this->requestUri = sprintf($this->requestUri, $limit, $offset);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
