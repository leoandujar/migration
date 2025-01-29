<?php

namespace App\Connector\Xtrf\Request\Quote;

use App\Connector\Xtrf\Request\Request;

class UpdateQuoteCustomFieldsRequest extends Request
{
	protected string $requestMethod = 'PUT';
	protected string $requestUri = '/quotes/%s/customFields';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $quoteId, array $params)
	{
		$this->params = $params;
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri = sprintf($this->requestUri, $quoteId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
