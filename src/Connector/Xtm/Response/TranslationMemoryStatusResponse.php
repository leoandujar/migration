<?php

namespace App\Connector\Xtm\Response;

class TranslationMemoryStatusResponse extends Response
{
	private bool $ready = false;

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			$this->ready = match ($rawResponse['status'] ?? false) {
				'FINISHED' => true,
				default => false
			};
		}
	}

	public function isReady(): bool
	{
		return $this->ready;
	}
}
