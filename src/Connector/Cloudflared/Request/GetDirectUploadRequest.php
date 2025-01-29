<?php

namespace App\Connector\Cloudflared\Request;

class GetDirectUploadRequest extends Request
{
	protected string $requestMethod = 'POST';
	protected string $requestUri = '/v2/direct_upload';
	protected string $type = Request::TYPE_MULTIPART;

	public function __construct(string $token)
	{
		$this->headers['Authorization'] = sprintf('Bearer %s', $token);
		parent::__construct($this->requestMethod, $this->requestUri, [], $this->headers);
	}
}
