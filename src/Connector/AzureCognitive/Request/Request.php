<?php

namespace App\Connector\AzureCognitive\Request;

class Request
{
	public const TYPE_JSON = 'json';
	public const TYPE_FORM = 'form';
	public const TYPE_MULTIPART = 'multipart';
	public const TYPE_BINARY = 'binary';

	protected string $requestMethod;
	protected string $requestUri;
	protected mixed $body = '';
	protected array $headers = [];
	protected mixed $params = null;
	protected int $timeout = 45;
	protected string $type = self::TYPE_FORM;

	public function __construct(string $requestMethod, string $requestUri, mixed $params, array $headers, $body = null)
	{
		$this->requestMethod = $requestMethod;
		$this->requestUri = $requestUri;
		$this->body = $body;
		$this->headers['Accept'] = 'application/*';
		$this->headers = array_merge($headers, $this->headers);
		$this->params = $params;
	}

	public function getRequestMethod(): string
	{
		return $this->requestMethod;
	}

	public function getRequestUri(): string
	{
		return $this->requestUri;
	}

	/**
	 * @return null
	 */
	public function getBody()
	{
		return $this->body;
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getParams(): mixed
	{
		return $this->params;
	}

	public function getTimeout(): int
	{
		return $this->timeout;
	}

	public function getType(): string
	{
		return $this->type;
	}
}
