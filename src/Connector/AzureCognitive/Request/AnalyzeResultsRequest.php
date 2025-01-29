<?php

namespace App\Connector\AzureCognitive\Request;

class AnalyzeResultsRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected string $requestUri = '/formrecognizer/documentModels/%s/analyzeResults/%s?api-version=2023-07-31';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $modelId, string $resultId)
	{
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri = sprintf($this->requestUri, $modelId, $resultId);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
