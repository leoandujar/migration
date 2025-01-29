<?php

namespace App\Connector\AzureCognitive\Request;

class AnalyzeDocumentRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/formrecognizer/documentModels/%s:analyze?%s';
	protected string $type = Request::TYPE_JSON;

	public function __construct(string $modelId, array $bodyParams, array $queryParams)
	{
		$this->params = $bodyParams;
		$this->headers['Content-Type'] = 'application/json';
		$this->requestUri = sprintf($this->requestUri, $modelId, http_build_query($queryParams));
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
