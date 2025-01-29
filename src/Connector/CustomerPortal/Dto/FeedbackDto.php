<?php

namespace App\Connector\CustomerPortal\Dto;

class FeedbackDto
{
	public const TYPE_CUSTOMER_CLAIM = 'CUSTOMER_CLAIM';

	public string $type;
	public array $targetLanguages;
	public string $description;

	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function setTargetLanguages(array $targetLanguages): self
	{
		$this->targetLanguages = $targetLanguages;

		return $this;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}
}
