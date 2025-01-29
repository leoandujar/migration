<?php

namespace App\Connector\XtrfMacro\Response;

class MacroRunResponse extends Response
{
	public ?string $actionId;
	public ?string $statusUrl;
	public ?string $resultUrl;
	public ?string $url;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);
	}

	public function translateRaw(): void
	{
		if ($this->isSuccessfull() && count($this->raw) && is_array($this->raw)) {
			$this->actionId = $this->raw['actionId'] ?? null;
			$this->statusUrl = $this->raw['statusUrl'] ?? null;
			$this->resultUrl = $this->raw['resultUrl'] ?? null;
			$this->url = $this->raw['url'] ?? null;
		}
	}
}
