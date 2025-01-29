<?php

namespace App\Connector\CustomerPortal\Response;

use App\Connector\CustomerPortal\Dto\QuoteDto;

class CreateQuoteResponse extends Response
{
	private QuoteDto $quoteDto;

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
			$this->quoteDto = new QuoteDto();
			$this->quoteDto
				->setId($this->raw['id'])
				->setCustomerNotes($this->raw['customerNotes'])
				->setDeadline($this->raw['deadline'])
				->setIdNumber($this->raw['idNumber'])
				->setLanguageCombinations($this->raw['languageCombinations'])
				->setName($this->raw['name'])
				->setOffice($this->raw['office'])
				->setService($this->raw['service'])
				->setSpecialization($this->raw['specialization'])
				->setStartDate($this->raw['startDate'])
				->setTmSavings($this->raw['tmSavings'])
				->setTotalAgreed($this->raw['totalAgreed'])
				->setWorkflow($this->raw['workflow'])
				->setAutoAccept($this->raw['autoAccept'])
				->setHasInputResources($this->raw['hasInputResources'])
				->setHasInputWorkfiles($this->raw['hasInputWorkfiles'])
				->setProjectManager($this->raw['projectManager'])
				->setQuoteConfirmationAvailable($this->raw['quoteConfirmationAvailable'])
				->setSalesPerson($this->raw['salesPerson'])
				->setStatus($this->raw['status']);
		}
	}

	public function getQuoteDto(): ?QuoteDto
	{
		return $this->quoteDto;
	}
}
