<?php

namespace App\Connector\Xtm\Response;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response
{
	protected ?int $httpCode;
	protected ?array $raw;
	protected ?string $errorMessage;
	protected ?string $detailedMessage;
	protected mixed $rawHeaders;

	public function __construct(int $httpCode, array $rawResponse, array $rawHeaders = [])
	{
		$this->httpCode   = $httpCode;
		$this->raw        = $rawResponse;
		$this->rawHeaders = $rawHeaders;
		$this->translateRaw();
	}

	public function getHttpCode(): int
	{
		return $this->httpCode;
	}

	public function getRaw(): array
	{
		return $this->raw;
	}

	public function isSuccessfull(): bool
	{
		return in_array($this->httpCode, [HttpResponse::HTTP_OK, HttpResponse::HTTP_CREATED, HttpResponse::HTTP_ACCEPTED, HttpResponse::HTTP_NO_CONTENT]);
	}

	public function translateRaw(): void
	{
		$this->errorMessage    = $this->raw['reason'] ?? null;
		$this->detailedMessage = $this->raw['reason'] ?? null;
	}

	public function getErrorMessage(): string
	{
		return $this->errorMessage ?? 'An error has occurred, but there is not enough information. Please check the logs for more details. Maybe API is down.';
	}

	public function getDetailedMessage(): string
	{
		return $this->detailedMessage ?? '';
	}
}
