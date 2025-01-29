<?php

namespace App\Connector\CustomerPortal\Response;

use App\Connector\CustomerPortal\Dto\SpecializationDto;

class SpecializationsListResponse extends Response
{
	private array $result = [];

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
			foreach ($this->raw as $item) {
				$this->result[] = (new SpecializationDto())
					->setId($item['id'])
					->setName($item['name'])
					->setLocalizedName($item['localizedName']);
			}
		}
	}

	/**
	 * @return SpecializationDto[]
	 */
	public function getResult(): array
	{
		return $this->result;
	}
}
