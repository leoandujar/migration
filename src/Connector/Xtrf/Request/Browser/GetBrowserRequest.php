<?php

namespace App\Connector\Xtrf\Request\Browser;

use App\Connector\Xtrf\Request\Request;

class GetBrowserRequest extends Request
{
	protected string $requestMethod = 'GET';
	protected int $timeout = 200;

	public function __construct(array $data)
	{
		$this->requestUri = '/browser/?'.http_build_query($data);
		parent::__construct($this->requestMethod, $this->requestUri, $this->params, $this->headers);
	}
}
