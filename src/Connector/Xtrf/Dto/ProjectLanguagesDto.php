<?php

namespace App\Connector\Xtrf\Dto;

class ProjectLanguagesDto
{
	public ?int $sourceLanguageId;
	public ?array $targetLanguageIds;
	public ?int $specializationId;
	public ?array $languageCombinations;

	public function setSourceLanguageId(int $sourceLanguageId): self
	{
		$this->sourceLanguageId = $sourceLanguageId;

		return $this;
	}

	public function setTargetLanguageIds(array $targetLanguageIds): self
	{
		$this->targetLanguageIds = $targetLanguageIds;

		return $this;
	}

	public function setSpecializationId(int $specializationId): self
	{
		$this->specializationId = $specializationId;

		return $this;
	}

	public function setLanguageCombinations(array $languageCombinations): self
	{
		$this->languageCombinations = $languageCombinations;

		return $this;
	}
}
