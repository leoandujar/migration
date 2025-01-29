<?php

namespace App\Connector\Xtm\Response;

use App\Connector\Xtm\XtmConnector;

class ProjectsCountResponse extends Response
{
	private const TOTAL_ELEMENTS_KEY = 'xtm-total-items-count';

	private int $totalElements      = 0;
	private int|float $totalPages = 0;

	public function __construct(int $httpCode, array $rawResponse, array $headers = [])
	{
		parent::__construct($httpCode, $rawResponse, $headers);

		if ($this->isSuccessfull()) {
			if ($headers[self::TOTAL_ELEMENTS_KEY]) {
				$this->totalElements = intval(array_shift($headers[self::TOTAL_ELEMENTS_KEY]));
				$this->totalPages    = ceil($this->totalElements / XtmConnector::MAX_PER_PAGE);
			}
		}
	}

	public function getTotalElements(): int
	{
		return $this->totalElements;
	}

	public function getTotalPages(): int
	{
		return $this->totalPages;
	}
}
