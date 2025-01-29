<?php

namespace App\Connector\Xtrf\Response\Quote;

use App\Connector\Xtrf\Dto\QuoteDto;
use App\Connector\Xtrf\Response\Response;

class GetQuoteResponse extends Response
{
	private ?QuoteDto $quote = null;

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
			$this->quote = new QuoteDto(
				$this->raw['id'],
				$this->raw['quoteId'],
				$this->raw['isClassicQuote'],
				$this->raw['idNumber'],
				$this->raw['name'],
				$this->raw['customerId'],
				$this->raw['contactPersonId'],
				$this->raw['automaticallyAcceptSentQuote'],
				$this->raw['categoriesIds'],
				$this->raw['finance'],
				$this->raw['customFields'],
				$this->raw['instructions'],
				$this->raw['tasks'],
			);
		}
	}

	public function getQuote(): ?QuoteDto
	{
		return $this->quote;
	}
}
