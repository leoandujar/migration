<?php

namespace App\Connector\Xtrf\Dto;

class QuoteDto
{
	/**
	 * QuoteDto constructor.
	 */
	public function __construct(
		public ?string $id,
		public ?string $quoteId,
		public ?bool $isClassicQuote,
		public ?string $idNumber,
		public ?string $name,
		public ?int $customerId,
		public ?int $contactPersonId,
		public ?bool $automaticallyAcceptSentQuote,
		public ?array $categoryIds = [],
		public ?array $finance = [],
		public ?array $customFields = [],
		public ?array $instructions = [],
		public ?array $tasks = [],
	) {
	}
}
