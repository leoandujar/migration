<?php

namespace App\Connector\Hubspot\Response\MarketingEmail;

use App\Connector\Xtrf\Response\Response;

class GetMarketingEmailResponse extends Response
{
	private array $objects;
	private int $totalCount = 0;
	private int $offset     = 0;
	private int $limit      = 0;

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
			$this->totalCount = $this->raw['totalCount'];
			$this->limit      = $this->raw['limit'];
			$this->offset     = $this->raw['offset'];
			$this->objects    = $this->raw['objects'];
		}
	}

	public function getObjects(): array
	{
		return $this->objects;
	}

	public function getTotalCount(): int
	{
		return $this->totalCount;
	}

	public function getOffset(): int
	{
		return $this->offset;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}
}
