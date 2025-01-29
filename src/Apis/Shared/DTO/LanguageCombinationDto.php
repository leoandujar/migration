<?php

namespace App\Apis\Shared\DTO;

class LanguageCombinationDto
{
	public LanguageDto $sourceLanguage;
	public LanguageDto $targetLanguage;

	public function setSourceLanguage(LanguageDto $sourceLanguage): self
	{
		$this->sourceLanguage = $sourceLanguage;

		return $this;
	}

	public function setTargetLanguage(LanguageDto $targetLanguage): self
	{
		$this->targetLanguage = $targetLanguage;

		return $this;
	}
}
