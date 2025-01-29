<?php

namespace App\Connector\Hubspot\Request\Companies;

use App\Connector\Hubspot\Request\Request;

class UpdateCompanyRequest extends Request
{
	protected string $requestMethod = 'PATCH';
	protected string $type = Request::TYPE_JSON;
	protected string $requestUri = '/crm/v3/objects/companies/%s';

	public function __construct(string $companyId, array $data)
	{
		$this->params = $data;
		$this->requestUri = sprintf($this->requestUri, $companyId);
		$this->headers['Content-Type'] = 'application/json';
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
