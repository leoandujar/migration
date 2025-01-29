<?php

namespace App\Connector\Xtrf\Response\Projects;

use App\Connector\Xtrf\Response\Response;

class UploadProjectFileResponse extends Response
{
	private string $token;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	/**
	 * @return mixed
	 */
	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$this->token = $this->raw['token'];
		}
	}

	public function getToken(): ?string
	{
		return $this->token;
	}
}
