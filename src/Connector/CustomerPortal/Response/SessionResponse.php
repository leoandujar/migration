<?php

namespace App\Connector\CustomerPortal\Response;

class SessionResponse extends Response
{
	private string $id;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->id = $rawResponse['id'];
		}
	}

	public function getId(): string
	{
		return $this->id;
	}
}
