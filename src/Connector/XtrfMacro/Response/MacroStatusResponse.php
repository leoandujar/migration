<?php

namespace App\Connector\XtrfMacro\Response;

class MacroStatusResponse extends Response
{
	public ?string $state;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$this->state = $this->raw['state'] ?? null;
		}
	}
}
