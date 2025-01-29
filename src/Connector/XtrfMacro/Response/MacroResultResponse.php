<?php

namespace App\Connector\XtrfMacro\Response;

class MacroResultResponse extends Response
{
	public ?string $url;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$this->url = $this->raw['url'] ?? null;
		}
	}
}
