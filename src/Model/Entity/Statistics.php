<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'statistics')]
#[ORM\Entity]
class Statistics implements EntityInterface
{
	public const T_SOURCE = 1;
	public const T_TARGET = 2;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'statistics_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'id', type: 'bigint')]
	private string $id;

	#[ORM\Column(type: 'smallint')]
	private int $type;

	#[ORM\ManyToOne(targetEntity: AnalyticsProjectStep::class, inversedBy: 'statistics')]
	#[ORM\JoinColumn(name: 'step', referencedColumnName: 'id', nullable: false)]
	private AnalyticsProjectStep $step;

	#[ORM\Column(type: 'integer')]
	private int $actualCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $actualTime;

	#[ORM\Column(type: 'integer')]
	private int $actualUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $actualWordCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlySpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyTime;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $alphaNumericOnlyWordCount;

	#[ORM\Column(type: 'integer')]
	private int $charactersDone;

	#[ORM\Column(type: 'string')]
	private string $date;

	#[ORM\Column(type: 'string')]
	private string $documentID;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchedCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchedSpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchedTime;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchedUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $exactMatchedWordCount;

	#[ORM\Column(name: 'fuzzy_c1time', type: 'integer')]
	private int $fuzzyC1Time;

	#[ORM\Column(name: 'fuzzy_c2time', type: 'integer')]
	private int $fuzzyC2Time;

	#[ORM\Column(name: 'fuzzy_c3time', type: 'integer')]
	private int $fuzzyC3Time;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedCharacterCountC1;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedCharacterCountC2;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedCharacterCountC3;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedSpacesCharacterCountC1;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedSpacesCharacterCountC2;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedSpacesCharacterCountC3;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedUnitCountC1;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedUnitCountC2;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedUnitCountC3;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedWordCountC1;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedWordCountC2;

	#[ORM\Column(type: 'integer')]
	private int $fuzzyMatchedWordCountC3;

	#[ORM\Column(type: 'integer')]
	private int $leveragedCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $leveragedMatchInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $leveragedMatchLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $leveragedSpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $leveragedTime;

	#[ORM\Column(type: 'integer')]
	private int $leveragedUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $leveragedWordCount;

	#[ORM\Column(type: 'string')]
	private string $locale;

	#[ORM\Column(type: 'integer')]
	private int $machineTranslationCharacters;

	#[ORM\Column(type: 'integer')]
	private int $machineTranslationTime;

	#[ORM\Column(type: 'integer')]
	private int $machineTranslationUnits;

	#[ORM\Column(type: 'integer')]
	private int $machineTranslationWhitespaces;

	#[ORM\Column(type: 'integer')]
	private int $machineTranslationWords;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlySpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyTime;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $measurementOnlyWordCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlySpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyTime;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $numericOnlyWordCount;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableSpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableTime;

	#[ORM\Column(type: 'integer')]
	private int $otherNonTranslatableWordCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyLinkingInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlySpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyTime;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $punctuationOnlyWordCount;

	#[ORM\Column(type: 'integer')]
	private int $realSpacesCharacterCount;

	#[ORM\Column(name: 'repetition_c1time', type: 'integer')]
	private int $repetitionC1Time;

	#[ORM\Column(name: 'repetition_c2time', type: 'integer')]
	private int $repetitionC2Time;

	#[ORM\Column(name: 'repetition_c3time', type: 'integer')]
	private int $repetitionC3Time;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedCharacterCountC1;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedCharacterCountC2;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedCharacterCountC3;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedSpacesCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedSpacesCharacterCountC1;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedSpacesCharacterCountC2;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedSpacesCharacterCountC3;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedUnitCountC1;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedUnitCountC2;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedUnitCountC3;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedWordCount;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedWordCountC1;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedWordCountC2;

	#[ORM\Column(type: 'integer')]
	private int $repetitionMatchedWordCountC3;

	#[ORM\Column(type: 'integer')]
	private int $repetitionTime;

	#[ORM\Column(type: 'integer')]
	private int $spacesCharactersDone;

	#[ORM\Column(type: 'integer')]
	private int $textUnitCount;

	#[ORM\Column(type: 'integer')]
	private int $totalCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $totalInlineElementCount;

	#[ORM\Column(type: 'integer')]
	private int $totalLinkingInlineElementCount;

	#[ORM\Column(type: 'integer')]
	private int $totalSpaceCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $totalWordCount;

	#[ORM\Column(type: 'integer')]
	private int $translatableInlineCount;

	#[ORM\Column(type: 'integer')]
	private int $unitsDone;

	#[ORM\Column(type: 'string')]
	private string $username;

	#[ORM\Column(type: 'string')]
	private string $version;

	#[ORM\Column(type: 'integer')]
	private int $whitespaceCharacterCount;

	#[ORM\Column(type: 'integer')]
	private int $wordsDone;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getType(): ?int
	{
		return $this->type;
	}

	public function setType(int $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getStep(): ?AnalyticsProjectStep
	{
		return $this->step;
	}

	public function setStep(?AnalyticsProjectStep $step): self
	{
		$this->step = $step;

		return $this;
	}

	public function getActualCharacterCount(): int
	{
		return $this->actualCharacterCount;
	}

	public function setActualCharacterCount(int $actualCharacterCount): self
	{
		$this->actualCharacterCount = $actualCharacterCount;

		return $this;
	}

	public function getActualTime(): int
	{
		return $this->actualTime;
	}

	public function setActualTime(int $actualTime): self
	{
		$this->actualTime = $actualTime;

		return $this;
	}

	public function getActualUnitCount(): int
	{
		return $this->actualUnitCount;
	}

	public function setActualUnitCount(int $actualUnitCount): self
	{
		$this->actualUnitCount = $actualUnitCount;

		return $this;
	}

	public function getActualWordCount(): int
	{
		return $this->actualWordCount;
	}

	public function setActualWordCount(int $actualWordCount): self
	{
		$this->actualWordCount = $actualWordCount;

		return $this;
	}

	public function getAlphaNumericOnlyCharacterCount(): int
	{
		return $this->alphaNumericOnlyCharacterCount;
	}

	public function setAlphaNumericOnlyCharacterCount(int $alphaNumericOnlyCharacterCount): self
	{
		$this->alphaNumericOnlyCharacterCount = $alphaNumericOnlyCharacterCount;

		return $this;
	}

	public function getAlphaNumericOnlyInlineCount(): int
	{
		return $this->alphaNumericOnlyInlineCount;
	}

	public function setAlphaNumericOnlyInlineCount(int $alphaNumericOnlyInlineCount): self
	{
		$this->alphaNumericOnlyInlineCount = $alphaNumericOnlyInlineCount;

		return $this;
	}

	public function getAlphaNumericOnlyLinkingInlineCount(): int
	{
		return $this->alphaNumericOnlyLinkingInlineCount;
	}

	public function setAlphaNumericOnlyLinkingInlineCount(int $alphaNumericOnlyLinkingInlineCount): self
	{
		$this->alphaNumericOnlyLinkingInlineCount = $alphaNumericOnlyLinkingInlineCount;

		return $this;
	}

	public function getAlphaNumericOnlySpacesCharacterCount(): int
	{
		return $this->alphaNumericOnlySpacesCharacterCount;
	}

	public function setAlphaNumericOnlySpacesCharacterCount(int $alphaNumericOnlySpacesCharacterCount): self
	{
		$this->alphaNumericOnlySpacesCharacterCount = $alphaNumericOnlySpacesCharacterCount;

		return $this;
	}

	public function getAlphaNumericOnlyTime(): int
	{
		return $this->alphaNumericOnlyTime;
	}

	public function setAlphaNumericOnlyTime(int $alphaNumericOnlyTime): self
	{
		$this->alphaNumericOnlyTime = $alphaNumericOnlyTime;

		return $this;
	}

	public function getAlphaNumericOnlyUnitCount(): int
	{
		return $this->alphaNumericOnlyUnitCount;
	}

	public function setAlphaNumericOnlyUnitCount(int $alphaNumericOnlyUnitCount): self
	{
		$this->alphaNumericOnlyUnitCount = $alphaNumericOnlyUnitCount;

		return $this;
	}

	public function getAlphaNumericOnlyWordCount(): int
	{
		return $this->alphaNumericOnlyWordCount;
	}

	public function setAlphaNumericOnlyWordCount(int $alphaNumericOnlyWordCount): self
	{
		$this->alphaNumericOnlyWordCount = $alphaNumericOnlyWordCount;

		return $this;
	}

	public function getCharactersDone(): int
	{
		return $this->charactersDone;
	}

	public function setCharactersDone(int $charactersDone): self
	{
		$this->charactersDone = $charactersDone;

		return $this;
	}

	public function getDate(): string
	{
		return $this->date;
	}

	public function setDate(string $date): self
	{
		$this->date = $date;

		return $this;
	}

	public function getDocumentID(): string
	{
		return $this->documentID;
	}

	public function setDocumentID(string $documentID): self
	{
		$this->documentID = $documentID;

		return $this;
	}

	public function getExactMatchInlineCount(): int
	{
		return $this->exactMatchInlineCount;
	}

	public function setExactMatchInlineCount(int $exactMatchInlineCount): self
	{
		$this->exactMatchInlineCount = $exactMatchInlineCount;

		return $this;
	}

	public function getExactMatchLinkingInlineCount(): int
	{
		return $this->exactMatchLinkingInlineCount;
	}

	public function setExactMatchLinkingInlineCount(int $exactMatchLinkingInlineCount): self
	{
		$this->exactMatchLinkingInlineCount = $exactMatchLinkingInlineCount;

		return $this;
	}

	public function getExactMatchedCharacterCount(): int
	{
		return $this->exactMatchedCharacterCount;
	}

	public function setExactMatchedCharacterCount(int $exactMatchedCharacterCount): self
	{
		$this->exactMatchedCharacterCount = $exactMatchedCharacterCount;

		return $this;
	}

	public function getExactMatchedSpacesCharacterCount(): int
	{
		return $this->exactMatchedSpacesCharacterCount;
	}

	public function setExactMatchedSpacesCharacterCount(int $exactMatchedSpacesCharacterCount): self
	{
		$this->exactMatchedSpacesCharacterCount = $exactMatchedSpacesCharacterCount;

		return $this;
	}

	public function getExactMatchedTime(): int
	{
		return $this->exactMatchedTime;
	}

	public function setExactMatchedTime(int $exactMatchedTime): self
	{
		$this->exactMatchedTime = $exactMatchedTime;

		return $this;
	}

	public function getExactMatchedUnitCount(): int
	{
		return $this->exactMatchedUnitCount;
	}

	public function setExactMatchedUnitCount(int $exactMatchedUnitCount): self
	{
		$this->exactMatchedUnitCount = $exactMatchedUnitCount;

		return $this;
	}

	public function getExactMatchedWordCount(): int
	{
		return $this->exactMatchedWordCount;
	}

	public function setExactMatchedWordCount(int $exactMatchedWordCount): self
	{
		$this->exactMatchedWordCount = $exactMatchedWordCount;

		return $this;
	}

	public function getFuzzyC1Time(): int
	{
		return $this->fuzzyC1Time;
	}

	public function setFuzzyC1Time(int $fuzzyC1Time): self
	{
		$this->fuzzyC1Time = $fuzzyC1Time;

		return $this;
	}

	public function getFuzzyC2Time(): int
	{
		return $this->fuzzyC2Time;
	}

	public function setFuzzyC2Time(int $fuzzyC2Time): self
	{
		$this->fuzzyC2Time = $fuzzyC2Time;

		return $this;
	}

	public function getFuzzyC3Time(): int
	{
		return $this->fuzzyC3Time;
	}

	public function setFuzzyC3Time(int $fuzzyC3Time): self
	{
		$this->fuzzyC3Time = $fuzzyC3Time;

		return $this;
	}

	public function getFuzzyMatchedCharacterCountC1(): int
	{
		return $this->fuzzyMatchedCharacterCountC1;
	}

	public function setFuzzyMatchedCharacterCountC1(int $fuzzyMatchedCharacterCountC1): self
	{
		$this->fuzzyMatchedCharacterCountC1 = $fuzzyMatchedCharacterCountC1;

		return $this;
	}

	public function getFuzzyMatchedCharacterCountC2(): int
	{
		return $this->fuzzyMatchedCharacterCountC2;
	}

	public function setFuzzyMatchedCharacterCountC2(int $fuzzyMatchedCharacterCountC2): self
	{
		$this->fuzzyMatchedCharacterCountC2 = $fuzzyMatchedCharacterCountC2;

		return $this;
	}

	public function getFuzzyMatchedCharacterCountC3(): int
	{
		return $this->fuzzyMatchedCharacterCountC3;
	}

	public function setFuzzyMatchedCharacterCountC3(int $fuzzyMatchedCharacterCountC3): self
	{
		$this->fuzzyMatchedCharacterCountC3 = $fuzzyMatchedCharacterCountC3;

		return $this;
	}

	public function getFuzzyMatchedSpacesCharacterCountC1(): int
	{
		return $this->fuzzyMatchedSpacesCharacterCountC1;
	}

	public function setFuzzyMatchedSpacesCharacterCountC1(int $fuzzyMatchedSpacesCharacterCountC1): self
	{
		$this->fuzzyMatchedSpacesCharacterCountC1 = $fuzzyMatchedSpacesCharacterCountC1;

		return $this;
	}

	public function getFuzzyMatchedSpacesCharacterCountC2(): int
	{
		return $this->fuzzyMatchedSpacesCharacterCountC2;
	}

	public function setFuzzyMatchedSpacesCharacterCountC2(int $fuzzyMatchedSpacesCharacterCountC2): self
	{
		$this->fuzzyMatchedSpacesCharacterCountC2 = $fuzzyMatchedSpacesCharacterCountC2;

		return $this;
	}

	public function getFuzzyMatchedSpacesCharacterCountC3(): int
	{
		return $this->fuzzyMatchedSpacesCharacterCountC3;
	}

	public function setFuzzyMatchedSpacesCharacterCountC3(int $fuzzyMatchedSpacesCharacterCountC3): self
	{
		$this->fuzzyMatchedSpacesCharacterCountC3 = $fuzzyMatchedSpacesCharacterCountC3;

		return $this;
	}

	public function getFuzzyMatchedUnitCountC1(): int
	{
		return $this->fuzzyMatchedUnitCountC1;
	}

	public function setFuzzyMatchedUnitCountC1(int $fuzzyMatchedUnitCountC1): self
	{
		$this->fuzzyMatchedUnitCountC1 = $fuzzyMatchedUnitCountC1;

		return $this;
	}

	public function getFuzzyMatchedUnitCountC2(): int
	{
		return $this->fuzzyMatchedUnitCountC2;
	}

	public function setFuzzyMatchedUnitCountC2(int $fuzzyMatchedUnitCountC2): self
	{
		$this->fuzzyMatchedUnitCountC2 = $fuzzyMatchedUnitCountC2;

		return $this;
	}

	public function getFuzzyMatchedUnitCountC3(): int
	{
		return $this->fuzzyMatchedUnitCountC3;
	}

	public function setFuzzyMatchedUnitCountC3(int $fuzzyMatchedUnitCountC3): self
	{
		$this->fuzzyMatchedUnitCountC3 = $fuzzyMatchedUnitCountC3;

		return $this;
	}

	public function getFuzzyMatchedWordCountC1(): int
	{
		return $this->fuzzyMatchedWordCountC1;
	}

	public function setFuzzyMatchedWordCountC1(int $fuzzyMatchedWordCountC1): self
	{
		$this->fuzzyMatchedWordCountC1 = $fuzzyMatchedWordCountC1;

		return $this;
	}

	public function getFuzzyMatchedWordCountC2(): int
	{
		return $this->fuzzyMatchedWordCountC2;
	}

	public function setFuzzyMatchedWordCountC2(int $fuzzyMatchedWordCountC2): self
	{
		$this->fuzzyMatchedWordCountC2 = $fuzzyMatchedWordCountC2;

		return $this;
	}

	public function getFuzzyMatchedWordCountC3(): int
	{
		return $this->fuzzyMatchedWordCountC3;
	}

	public function setFuzzyMatchedWordCountC3(int $fuzzyMatchedWordCountC3): self
	{
		$this->fuzzyMatchedWordCountC3 = $fuzzyMatchedWordCountC3;

		return $this;
	}

	public function getLeveragedCharacterCount(): int
	{
		return $this->leveragedCharacterCount;
	}

	public function setLeveragedCharacterCount(int $leveragedCharacterCount): self
	{
		$this->leveragedCharacterCount = $leveragedCharacterCount;

		return $this;
	}

	public function getLeveragedMatchInlineCount(): int
	{
		return $this->leveragedMatchInlineCount;
	}

	public function setLeveragedMatchInlineCount(int $leveragedMatchInlineCount): self
	{
		$this->leveragedMatchInlineCount = $leveragedMatchInlineCount;

		return $this;
	}

	public function getLeveragedMatchLinkingInlineCount(): int
	{
		return $this->leveragedMatchLinkingInlineCount;
	}

	public function setLeveragedMatchLinkingInlineCount(int $leveragedMatchLinkingInlineCount): self
	{
		$this->leveragedMatchLinkingInlineCount = $leveragedMatchLinkingInlineCount;

		return $this;
	}

	public function getLeveragedSpacesCharacterCount(): int
	{
		return $this->leveragedSpacesCharacterCount;
	}

	public function setLeveragedSpacesCharacterCount(int $leveragedSpacesCharacterCount): self
	{
		$this->leveragedSpacesCharacterCount = $leveragedSpacesCharacterCount;

		return $this;
	}

	public function getLeveragedTime(): int
	{
		return $this->leveragedTime;
	}

	public function setLeveragedTime(int $leveragedTime): self
	{
		$this->leveragedTime = $leveragedTime;

		return $this;
	}

	public function getLeveragedUnitCount(): int
	{
		return $this->leveragedUnitCount;
	}

	public function setLeveragedUnitCount(int $leveragedUnitCount): self
	{
		$this->leveragedUnitCount = $leveragedUnitCount;

		return $this;
	}

	public function getLeveragedWordCount(): int
	{
		return $this->leveragedWordCount;
	}

	public function setLeveragedWordCount(int $leveragedWordCount): self
	{
		$this->leveragedWordCount = $leveragedWordCount;

		return $this;
	}

	public function getLocale(): string
	{
		return $this->locale;
	}

	public function setLocale(string $locale): self
	{
		$this->locale = $locale;

		return $this;
	}

	public function getMachineTranslationCharacters(): int
	{
		return $this->machineTranslationCharacters;
	}

	public function setMachineTranslationCharacters(int $machineTranslationCharacters): self
	{
		$this->machineTranslationCharacters = $machineTranslationCharacters;

		return $this;
	}

	public function getMachineTranslationTime(): int
	{
		return $this->machineTranslationTime;
	}

	public function setMachineTranslationTime(int $machineTranslationTime): self
	{
		$this->machineTranslationTime = $machineTranslationTime;

		return $this;
	}

	public function getMachineTranslationUnits(): int
	{
		return $this->machineTranslationUnits;
	}

	public function setMachineTranslationUnits(int $machineTranslationUnits): self
	{
		$this->machineTranslationUnits = $machineTranslationUnits;

		return $this;
	}

	public function getMachineTranslationWhitespaces(): int
	{
		return $this->machineTranslationWhitespaces;
	}

	public function setMachineTranslationWhitespaces(int $machineTranslationWhitespaces): self
	{
		$this->machineTranslationWhitespaces = $machineTranslationWhitespaces;

		return $this;
	}

	public function getMachineTranslationWords(): int
	{
		return $this->machineTranslationWords;
	}

	public function setMachineTranslationWords(int $machineTranslationWords): self
	{
		$this->machineTranslationWords = $machineTranslationWords;

		return $this;
	}

	public function getMeasurementOnlyCharacterCount(): int
	{
		return $this->measurementOnlyCharacterCount;
	}

	public function setMeasurementOnlyCharacterCount(int $measurementOnlyCharacterCount): self
	{
		$this->measurementOnlyCharacterCount = $measurementOnlyCharacterCount;

		return $this;
	}

	public function getMeasurementOnlyInlineCount(): int
	{
		return $this->measurementOnlyInlineCount;
	}

	public function setMeasurementOnlyInlineCount(int $measurementOnlyInlineCount): self
	{
		$this->measurementOnlyInlineCount = $measurementOnlyInlineCount;

		return $this;
	}

	public function getMeasurementOnlyLinkingInlineCount(): int
	{
		return $this->measurementOnlyLinkingInlineCount;
	}

	public function setMeasurementOnlyLinkingInlineCount(int $measurementOnlyLinkingInlineCount): self
	{
		$this->measurementOnlyLinkingInlineCount = $measurementOnlyLinkingInlineCount;

		return $this;
	}

	public function getMeasurementOnlySpacesCharacterCount(): int
	{
		return $this->measurementOnlySpacesCharacterCount;
	}

	public function setMeasurementOnlySpacesCharacterCount(int $measurementOnlySpacesCharacterCount): self
	{
		$this->measurementOnlySpacesCharacterCount = $measurementOnlySpacesCharacterCount;

		return $this;
	}

	public function getMeasurementOnlyTime(): int
	{
		return $this->measurementOnlyTime;
	}

	public function setMeasurementOnlyTime(int $measurementOnlyTime): self
	{
		$this->measurementOnlyTime = $measurementOnlyTime;

		return $this;
	}

	public function getMeasurementOnlyUnitCount(): int
	{
		return $this->measurementOnlyUnitCount;
	}

	public function setMeasurementOnlyUnitCount(int $measurementOnlyUnitCount): self
	{
		$this->measurementOnlyUnitCount = $measurementOnlyUnitCount;

		return $this;
	}

	public function getMeasurementOnlyWordCount(): int
	{
		return $this->measurementOnlyWordCount;
	}

	public function setMeasurementOnlyWordCount(int $measurementOnlyWordCount): self
	{
		$this->measurementOnlyWordCount = $measurementOnlyWordCount;

		return $this;
	}

	public function getNumericOnlyCharacterCount(): int
	{
		return $this->numericOnlyCharacterCount;
	}

	public function setNumericOnlyCharacterCount(int $numericOnlyCharacterCount): self
	{
		$this->numericOnlyCharacterCount = $numericOnlyCharacterCount;

		return $this;
	}

	public function getNumericOnlyInlineCount(): int
	{
		return $this->numericOnlyInlineCount;
	}

	public function setNumericOnlyInlineCount(int $numericOnlyInlineCount): self
	{
		$this->numericOnlyInlineCount = $numericOnlyInlineCount;

		return $this;
	}

	public function getNumericOnlyLinkingInlineCount(): int
	{
		return $this->numericOnlyLinkingInlineCount;
	}

	public function setNumericOnlyLinkingInlineCount(int $numericOnlyLinkingInlineCount): self
	{
		$this->numericOnlyLinkingInlineCount = $numericOnlyLinkingInlineCount;

		return $this;
	}

	public function getNumericOnlySpacesCharacterCount(): int
	{
		return $this->numericOnlySpacesCharacterCount;
	}

	public function setNumericOnlySpacesCharacterCount(int $numericOnlySpacesCharacterCount): self
	{
		$this->numericOnlySpacesCharacterCount = $numericOnlySpacesCharacterCount;

		return $this;
	}

	public function getNumericOnlyTime(): int
	{
		return $this->numericOnlyTime;
	}

	public function setNumericOnlyTime(int $numericOnlyTime): self
	{
		$this->numericOnlyTime = $numericOnlyTime;

		return $this;
	}

	public function getNumericOnlyUnitCount(): int
	{
		return $this->numericOnlyUnitCount;
	}

	public function setNumericOnlyUnitCount(int $numericOnlyUnitCount): self
	{
		$this->numericOnlyUnitCount = $numericOnlyUnitCount;

		return $this;
	}

	public function getNumericOnlyWordCount(): int
	{
		return $this->numericOnlyWordCount;
	}

	public function setNumericOnlyWordCount(int $numericOnlyWordCount): self
	{
		$this->numericOnlyWordCount = $numericOnlyWordCount;

		return $this;
	}

	public function getOtherNonTranslatableCharacterCount(): int
	{
		return $this->otherNonTranslatableCharacterCount;
	}

	public function setOtherNonTranslatableCharacterCount(int $otherNonTranslatableCharacterCount): self
	{
		$this->otherNonTranslatableCharacterCount = $otherNonTranslatableCharacterCount;

		return $this;
	}

	public function getOtherNonTranslatableInlineCount(): int
	{
		return $this->otherNonTranslatableInlineCount;
	}

	public function setOtherNonTranslatableInlineCount(int $otherNonTranslatableInlineCount): self
	{
		$this->otherNonTranslatableInlineCount = $otherNonTranslatableInlineCount;

		return $this;
	}

	public function getOtherNonTranslatableLinkingInlineCount(): int
	{
		return $this->otherNonTranslatableLinkingInlineCount;
	}

	public function setOtherNonTranslatableLinkingInlineCount(int $otherNonTranslatableLinkingInlineCount): self
	{
		$this->otherNonTranslatableLinkingInlineCount = $otherNonTranslatableLinkingInlineCount;

		return $this;
	}

	public function getOtherNonTranslatableSpacesCharacterCount(): int
	{
		return $this->otherNonTranslatableSpacesCharacterCount;
	}

	public function setOtherNonTranslatableSpacesCharacterCount(int $otherNonTranslatableSpacesCharacterCount): self
	{
		$this->otherNonTranslatableSpacesCharacterCount = $otherNonTranslatableSpacesCharacterCount;

		return $this;
	}

	public function getOtherNonTranslatableTime(): int
	{
		return $this->otherNonTranslatableTime;
	}

	public function setOtherNonTranslatableTime(int $otherNonTranslatableTime): self
	{
		$this->otherNonTranslatableTime = $otherNonTranslatableTime;

		return $this;
	}

	public function getOtherNonTranslatableWordCount(): int
	{
		return $this->otherNonTranslatableWordCount;
	}

	public function setOtherNonTranslatableWordCount(int $otherNonTranslatableWordCount): self
	{
		$this->otherNonTranslatableWordCount = $otherNonTranslatableWordCount;

		return $this;
	}

	public function getPunctuationCharacterCount(): int
	{
		return $this->punctuationCharacterCount;
	}

	public function setPunctuationCharacterCount(int $punctuationCharacterCount): self
	{
		$this->punctuationCharacterCount = $punctuationCharacterCount;

		return $this;
	}

	public function getPunctuationOnlyCharacterCount(): int
	{
		return $this->punctuationOnlyCharacterCount;
	}

	public function setPunctuationOnlyCharacterCount(int $punctuationOnlyCharacterCount): self
	{
		$this->punctuationOnlyCharacterCount = $punctuationOnlyCharacterCount;

		return $this;
	}

	public function getPunctuationOnlyInlineCount(): int
	{
		return $this->punctuationOnlyInlineCount;
	}

	public function setPunctuationOnlyInlineCount(int $punctuationOnlyInlineCount): self
	{
		$this->punctuationOnlyInlineCount = $punctuationOnlyInlineCount;

		return $this;
	}

	public function getPunctuationOnlyLinkingInlineCount(): int
	{
		return $this->punctuationOnlyLinkingInlineCount;
	}

	public function setPunctuationOnlyLinkingInlineCount(int $punctuationOnlyLinkingInlineCount): self
	{
		$this->punctuationOnlyLinkingInlineCount = $punctuationOnlyLinkingInlineCount;

		return $this;
	}

	public function getPunctuationOnlySpacesCharacterCount(): int
	{
		return $this->punctuationOnlySpacesCharacterCount;
	}

	public function setPunctuationOnlySpacesCharacterCount(int $punctuationOnlySpacesCharacterCount): self
	{
		$this->punctuationOnlySpacesCharacterCount = $punctuationOnlySpacesCharacterCount;

		return $this;
	}

	public function getPunctuationOnlyTime(): int
	{
		return $this->punctuationOnlyTime;
	}

	public function setPunctuationOnlyTime(int $punctuationOnlyTime): self
	{
		$this->punctuationOnlyTime = $punctuationOnlyTime;

		return $this;
	}

	public function getPunctuationOnlyUnitCount(): int
	{
		return $this->punctuationOnlyUnitCount;
	}

	public function setPunctuationOnlyUnitCount(int $punctuationOnlyUnitCount): self
	{
		$this->punctuationOnlyUnitCount = $punctuationOnlyUnitCount;

		return $this;
	}

	public function getPunctuationOnlyWordCount(): int
	{
		return $this->punctuationOnlyWordCount;
	}

	public function setPunctuationOnlyWordCount(int $punctuationOnlyWordCount): self
	{
		$this->punctuationOnlyWordCount = $punctuationOnlyWordCount;

		return $this;
	}

	public function getRealSpacesCharacterCount(): int
	{
		return $this->realSpacesCharacterCount;
	}

	public function setRealSpacesCharacterCount(int $realSpacesCharacterCount): self
	{
		$this->realSpacesCharacterCount = $realSpacesCharacterCount;

		return $this;
	}

	public function getRepetitionC1Time(): int
	{
		return $this->repetitionC1Time;
	}

	public function setRepetitionC1Time(int $repetitionC1Time): self
	{
		$this->repetitionC1Time = $repetitionC1Time;

		return $this;
	}

	public function getRepetitionC2Time(): int
	{
		return $this->repetitionC2Time;
	}

	public function setRepetitionC2Time(int $repetitionC2Time): self
	{
		$this->repetitionC2Time = $repetitionC2Time;

		return $this;
	}

	public function getRepetitionC3Time(): int
	{
		return $this->repetitionC3Time;
	}

	public function setRepetitionC3Time(int $repetitionC3Time): self
	{
		$this->repetitionC3Time = $repetitionC3Time;

		return $this;
	}

	public function getRepetitionMatchedCharacterCount(): int
	{
		return $this->repetitionMatchedCharacterCount;
	}

	public function setRepetitionMatchedCharacterCount(int $repetitionMatchedCharacterCount): self
	{
		$this->repetitionMatchedCharacterCount = $repetitionMatchedCharacterCount;

		return $this;
	}

	public function getRepetitionMatchedCharacterCountC1(): int
	{
		return $this->repetitionMatchedCharacterCountC1;
	}

	public function setRepetitionMatchedCharacterCountC1(int $repetitionMatchedCharacterCountC1): self
	{
		$this->repetitionMatchedCharacterCountC1 = $repetitionMatchedCharacterCountC1;

		return $this;
	}

	public function getRepetitionMatchedCharacterCountC2(): int
	{
		return $this->repetitionMatchedCharacterCountC2;
	}

	public function setRepetitionMatchedCharacterCountC2(int $repetitionMatchedCharacterCountC2): self
	{
		$this->repetitionMatchedCharacterCountC2 = $repetitionMatchedCharacterCountC2;

		return $this;
	}

	public function getRepetitionMatchedCharacterCountC3(): int
	{
		return $this->repetitionMatchedCharacterCountC3;
	}

	public function setRepetitionMatchedCharacterCountC3(int $repetitionMatchedCharacterCountC3): self
	{
		$this->repetitionMatchedCharacterCountC3 = $repetitionMatchedCharacterCountC3;

		return $this;
	}

	public function getRepetitionMatchedSpacesCharacterCount(): int
	{
		return $this->repetitionMatchedSpacesCharacterCount;
	}

	public function setRepetitionMatchedSpacesCharacterCount(int $repetitionMatchedSpacesCharacterCount): self
	{
		$this->repetitionMatchedSpacesCharacterCount = $repetitionMatchedSpacesCharacterCount;

		return $this;
	}

	public function getRepetitionMatchedSpacesCharacterCountC1(): int
	{
		return $this->repetitionMatchedSpacesCharacterCountC1;
	}

	public function setRepetitionMatchedSpacesCharacterCountC1(int $repetitionMatchedSpacesCharacterCountC1): self
	{
		$this->repetitionMatchedSpacesCharacterCountC1 = $repetitionMatchedSpacesCharacterCountC1;

		return $this;
	}

	public function getRepetitionMatchedSpacesCharacterCountC2(): int
	{
		return $this->repetitionMatchedSpacesCharacterCountC2;
	}

	public function setRepetitionMatchedSpacesCharacterCountC2(int $repetitionMatchedSpacesCharacterCountC2): self
	{
		$this->repetitionMatchedSpacesCharacterCountC2 = $repetitionMatchedSpacesCharacterCountC2;

		return $this;
	}

	public function getRepetitionMatchedSpacesCharacterCountC3(): int
	{
		return $this->repetitionMatchedSpacesCharacterCountC3;
	}

	public function setRepetitionMatchedSpacesCharacterCountC3(int $repetitionMatchedSpacesCharacterCountC3): self
	{
		$this->repetitionMatchedSpacesCharacterCountC3 = $repetitionMatchedSpacesCharacterCountC3;

		return $this;
	}

	public function getRepetitionMatchedUnitCount(): int
	{
		return $this->repetitionMatchedUnitCount;
	}

	public function setRepetitionMatchedUnitCount(int $repetitionMatchedUnitCount): self
	{
		$this->repetitionMatchedUnitCount = $repetitionMatchedUnitCount;

		return $this;
	}

	public function getRepetitionMatchedUnitCountC1(): int
	{
		return $this->repetitionMatchedUnitCountC1;
	}

	public function setRepetitionMatchedUnitCountC1(int $repetitionMatchedUnitCountC1): self
	{
		$this->repetitionMatchedUnitCountC1 = $repetitionMatchedUnitCountC1;

		return $this;
	}

	public function getRepetitionMatchedUnitCountC2(): int
	{
		return $this->repetitionMatchedUnitCountC2;
	}

	public function setRepetitionMatchedUnitCountC2(int $repetitionMatchedUnitCountC2): self
	{
		$this->repetitionMatchedUnitCountC2 = $repetitionMatchedUnitCountC2;

		return $this;
	}

	public function getRepetitionMatchedUnitCountC3(): int
	{
		return $this->repetitionMatchedUnitCountC3;
	}

	public function setRepetitionMatchedUnitCountC3(int $repetitionMatchedUnitCountC3): self
	{
		$this->repetitionMatchedUnitCountC3 = $repetitionMatchedUnitCountC3;

		return $this;
	}

	public function getRepetitionMatchedWordCount(): int
	{
		return $this->repetitionMatchedWordCount;
	}

	public function setRepetitionMatchedWordCount(int $repetitionMatchedWordCount): self
	{
		$this->repetitionMatchedWordCount = $repetitionMatchedWordCount;

		return $this;
	}

	public function getRepetitionMatchedWordCountC1(): int
	{
		return $this->repetitionMatchedWordCountC1;
	}

	public function setRepetitionMatchedWordCountC1(int $repetitionMatchedWordCountC1): self
	{
		$this->repetitionMatchedWordCountC1 = $repetitionMatchedWordCountC1;

		return $this;
	}

	public function getRepetitionMatchedWordCountC2(): int
	{
		return $this->repetitionMatchedWordCountC2;
	}

	public function setRepetitionMatchedWordCountC2(int $repetitionMatchedWordCountC2): self
	{
		$this->repetitionMatchedWordCountC2 = $repetitionMatchedWordCountC2;

		return $this;
	}

	public function getRepetitionMatchedWordCountC3(): int
	{
		return $this->repetitionMatchedWordCountC3;
	}

	public function setRepetitionMatchedWordCountC3(int $repetitionMatchedWordCountC3): self
	{
		$this->repetitionMatchedWordCountC3 = $repetitionMatchedWordCountC3;

		return $this;
	}

	public function getRepetitionTime(): int
	{
		return $this->repetitionTime;
	}

	public function setRepetitionTime(int $repetitionTime): self
	{
		$this->repetitionTime = $repetitionTime;

		return $this;
	}

	public function getSpacesCharactersDone(): int
	{
		return $this->spacesCharactersDone;
	}

	public function setSpacesCharactersDone(int $spacesCharactersDone): self
	{
		$this->spacesCharactersDone = $spacesCharactersDone;

		return $this;
	}

	public function getTextUnitCount(): int
	{
		return $this->textUnitCount;
	}

	public function setTextUnitCount(int $textUnitCount): self
	{
		$this->textUnitCount = $textUnitCount;

		return $this;
	}

	public function getTotalCharacterCount(): int
	{
		return $this->totalCharacterCount;
	}

	public function setTotalCharacterCount(int $totalCharacterCount): self
	{
		$this->totalCharacterCount = $totalCharacterCount;

		return $this;
	}

	public function getTotalInlineElementCount(): int
	{
		return $this->totalInlineElementCount;
	}

	public function setTotalInlineElementCount(int $totalInlineElementCount): self
	{
		$this->totalInlineElementCount = $totalInlineElementCount;

		return $this;
	}

	public function getTotalLinkingInlineElementCount(): int
	{
		return $this->totalLinkingInlineElementCount;
	}

	public function setTotalLinkingInlineElementCount(int $totalLinkingInlineElementCount): self
	{
		$this->totalLinkingInlineElementCount = $totalLinkingInlineElementCount;

		return $this;
	}

	public function getTotalSpaceCharacterCount(): int
	{
		return $this->totalSpaceCharacterCount;
	}

	public function setTotalSpaceCharacterCount(int $totalSpaceCharacterCount): self
	{
		$this->totalSpaceCharacterCount = $totalSpaceCharacterCount;

		return $this;
	}

	public function getTotalWordCount(): int
	{
		return $this->totalWordCount;
	}

	public function setTotalWordCount(int $totalWordCount): self
	{
		$this->totalWordCount = $totalWordCount;

		return $this;
	}

	public function getTranslatableInlineCount(): int
	{
		return $this->translatableInlineCount;
	}

	public function setTranslatableInlineCount(int $translatableInlineCount): self
	{
		$this->translatableInlineCount = $translatableInlineCount;

		return $this;
	}

	public function getUnitsDone(): int
	{
		return $this->unitsDone;
	}

	public function setUnitsDone(int $unitsDone): self
	{
		$this->unitsDone = $unitsDone;

		return $this;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): self
	{
		$this->username = $username;

		return $this;
	}

	public function getVersion(): string
	{
		return $this->version;
	}

	public function setVersion(string $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function getWhitespaceCharacterCount(): int
	{
		return $this->whitespaceCharacterCount;
	}

	public function setWhitespaceCharacterCount(int $whitespaceCharacterCount): self
	{
		$this->whitespaceCharacterCount = $whitespaceCharacterCount;

		return $this;
	}

	public function getWordsDone(): int
	{
		return $this->wordsDone;
	}

	public function setWordsDone(int $wordsDone): self
	{
		$this->wordsDone = $wordsDone;

		return $this;
	}
}
