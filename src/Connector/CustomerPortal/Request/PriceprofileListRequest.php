<?php

namespace App\Connector\CustomerPortal\Request;

class PriceprofileListRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri    = '/customers/%s/sales/priceProfiles';

	public function __construct(string $id, ?string $sessionId)
	{
		$this->headers['Cookie'] = sprintf('JSESSIONID=%s', $sessionId);
		$this->requestUri        = sprintf($this->requestUri, $id);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
