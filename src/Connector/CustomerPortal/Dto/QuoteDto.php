<?php

namespace App\Connector\CustomerPortal\Dto;

class QuoteDto
{
	public ?string $id;
	public ?string $customerNotes;
	public ?array $deadline = [];
	public ?string $idNumber;
	public ?array $languageCombinations = [];
	public ?string $name;
	public ?array $office = [];
	public ?string $service;
	public ?string $specialization;
	public ?array $startDate   = [];
	public ?array $tmSavings   = [];
	public ?array $totalAgreed = [];
	public ?string $workflow;
	public ?string $autoAccept;
	public ?string $hasInputResources;
	public ?string $hasInputWorkfiles;
	public ?array $projectManager = [];
	public ?string $quoteConfirmationAvailable;
	public ?array $salesPerson = [];
	public ?string $status;

	public function setId(?string $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function setCustomerNotes(?string $customerNotes): self
	{
		$this->customerNotes = $customerNotes;

		return $this;
	}

	public function setDeadline(?array $deadline): self
	{
		$this->deadline = $deadline;

		return $this;
	}

	public function setIdNumber(?string $idNumber): self
	{
		$this->idNumber = $idNumber;

		return $this;
	}

	public function setLanguageCombinations(?array $languageCombinations): self
	{
		$this->languageCombinations = $languageCombinations;

		return $this;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function setOffice(?array $office): self
	{
		$this->office = $office;

		return $this;
	}

	public function setService(?string $service): self
	{
		$this->service = $service;

		return $this;
	}

	public function setSpecialization(?string $specialization): self
	{
		$this->specialization = $specialization;

		return $this;
	}

	public function setStartDate(?array $startDate): self
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function setTmSavings(?array $tmSavings): self
	{
		$this->tmSavings = $tmSavings;

		return $this;
	}

	public function setTotalAgreed(?array $totalAgreed): self
	{
		$this->totalAgreed = $totalAgreed;

		return $this;
	}

	public function setWorkflow(?string $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}

	public function setAutoAccept(?string $autoAccept): self
	{
		$this->autoAccept = $autoAccept;

		return $this;
	}

	public function setHasInputResources(?string $hasInputResources): self
	{
		$this->hasInputResources = $hasInputResources;

		return $this;
	}

	public function setHasInputWorkfiles(?string $hasInputWorkfiles): self
	{
		$this->hasInputWorkfiles = $hasInputWorkfiles;

		return $this;
	}

	public function setProjectManager(?array $projectManager): self
	{
		$this->projectManager = $projectManager;

		return $this;
	}

	public function setQuoteConfirmationAvailable(?string $quoteConfirmationAvailable): self
	{
		$this->quoteConfirmationAvailable = $quoteConfirmationAvailable;

		return $this;
	}

	public function setSalesPerson(?array $salesPerson): self
	{
		$this->salesPerson = $salesPerson;

		return $this;
	}

	public function setStatus(?string $status): self
	{
		$this->status = $status;

		return $this;
	}
}
