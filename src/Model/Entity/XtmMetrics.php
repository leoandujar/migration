<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'xtm_metrics')]
#[ORM\Entity(repositoryClass: 'App\Model\Repository\XtmMetricsRepository')]
class XtmMetrics implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'xtm_metrics_id', type: 'guid')]
	private string $id;

	#[ORM\Column(name: 'external_id', type: 'integer')]
	private int $externalId;

	#[ORM\Column(name: 'target_language_code', type: 'string', length: 50, nullable: false)]
	private string $targetLanguageCode;

	#[ORM\ManyToOne(targetEntity: AnalyticsProject::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'analytics_project_id', nullable: false)]
	private AnalyticsProject $analyticsProject;

	#[ORM\Column(name: 'ice_match_characters', type: 'integer', nullable: false)]
	private int $iceMatchCharacters = 0;

	#[ORM\Column(name: 'ice_match_segments', type: 'integer', nullable: false)]
	private int $iceMatchSegments = 0;

	#[ORM\Column(name: 'ice_match_words', type: 'integer', nullable: false)]
	private int $iceMatchWords = 0;

	#[ORM\Column(name: 'ice_match_whitespaces', type: 'integer', nullable: false)]
	private int $iceMatchWhitespaces = 0;

	#[ORM\Column(name: 'low_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'low_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'low_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchWords = 0;

	#[ORM\Column(name: 'low_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchWords = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'high_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $highFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'high_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $highFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'high_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $highFuzzyMatchWords = 0;

	#[ORM\Column(name: 'high_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $highFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'repeats_characters', type: 'integer', nullable: false)]
	private int $repeatsCharacters = 0;

	#[ORM\Column(name: 'repeats_segments', type: 'integer', nullable: false)]
	private int $repeatsSegments = 0;

	#[ORM\Column(name: 'repeats_words', type: 'integer', nullable: false)]
	private int $repeatsWords = 0;

	#[ORM\Column(name: 'repeats_whitespaces', type: 'integer', nullable: false)]
	private int $repeatsWhitespaces = 0;

	#[ORM\Column(name: 'leveraged_characters', type: 'integer', nullable: false)]
	private int $leveragedCharacters = 0;

	#[ORM\Column(name: 'leveraged_segments', type: 'integer', nullable: false)]
	private int $leveragedSegments = 0;

	#[ORM\Column(name: 'leveraged_words', type: 'integer', nullable: false)]
	private int $leveragedWords = 0;

	#[ORM\Column(name: 'leveraged_whitespaces', type: 'integer', nullable: false)]
	private int $leveragedWhitespaces = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'non_translatable_characters', type: 'integer', nullable: false)]
	private int $nonTranslatableCharacters = 0;

	#[ORM\Column(name: 'non_translatable_segments', type: 'integer', nullable: false)]
	private int $nonTranslatableSegments = 0;

	#[ORM\Column(name: 'non_translatable_words', type: 'integer', nullable: false)]
	private int $nonTranslatableWords = 0;

	#[ORM\Column(name: 'non_translatable_whitespaces', type: 'integer', nullable: false)]
	private int $nonTranslatableWhitespaces = 0;

	#[ORM\Column(name: 'total_characters', type: 'integer', nullable: false)]
	private int $totalCharacters = 0;

	#[ORM\Column(name: 'total_segments', type: 'integer', nullable: false)]
	private int $totalSegments = 0;

	#[ORM\Column(name: 'total_words', type: 'integer', nullable: false)]
	private int $totalWords = 0;

	#[ORM\Column(name: 'total_whitespaces', type: 'integer', nullable: false)]
	private int $totalWhitespaces = 0;

	#[ORM\Column(name: 'machine_translation_characters', type: 'integer', nullable: false)]
	private int $machineTranslationCharacters = 0;

	#[ORM\Column(name: 'machine_translation_segments', type: 'integer', nullable: false)]
	private int $machineTranslationSegments = 0;

	#[ORM\Column(name: 'machine_translation_words', type: 'integer', nullable: false)]
	private int $machineTranslationWords = 0;

	#[ORM\Column(name: 'machine_translation_whitespaces', type: 'integer', nullable: false)]
	private int $machineTranslationWhitespaces = 0;

	#[ORM\Column(name: 'no_match_characters', type: 'integer', nullable: false)]
	private int $noMatchCharacters = 0;

	#[ORM\Column(name: 'no_match_segments', type: 'integer', nullable: false)]
	private int $noMatchSegments = 0;

	#[ORM\Column(name: 'no_match_words', type: 'integer', nullable: false)]
	private int $noMatchWords = 0;

	#[ORM\Column(name: 'no_match_whitespaces', type: 'integer', nullable: false)]
	private int $noMatchWhitespaces = 0;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getExternalId(): ?int
	{
		return $this->externalId;
	}

	public function setExternalId(int $externalId): self
	{
		$this->externalId = $externalId;

		return $this;
	}

	public function getTargetLanguageCode(): ?string
	{
		return $this->targetLanguageCode;
	}

	public function setTargetLanguageCode(string $targetLanguageCode): self
	{
		$this->targetLanguageCode = $targetLanguageCode;

		return $this;
	}

	public function getIceMatchCharacters(): ?int
	{
		return $this->iceMatchCharacters;
	}

	public function setIceMatchCharacters(int $iceMatchCharacters): self
	{
		$this->iceMatchCharacters = $iceMatchCharacters;

		return $this;
	}

	public function getIceMatchSegments(): ?int
	{
		return $this->iceMatchSegments;
	}

	public function setIceMatchSegments(int $iceMatchSegments): self
	{
		$this->iceMatchSegments = $iceMatchSegments;

		return $this;
	}

	public function getIceMatchWords(): ?int
	{
		return $this->iceMatchWords;
	}

	public function setIceMatchWords(int $iceMatchWords): self
	{
		$this->iceMatchWords = $iceMatchWords;

		return $this;
	}

	public function getIceMatchWhitespaces(): ?int
	{
		return $this->iceMatchWhitespaces;
	}

	public function setIceMatchWhitespaces(int $iceMatchWhitespaces): self
	{
		$this->iceMatchWhitespaces = $iceMatchWhitespaces;

		return $this;
	}

	public function getLowFuzzyMatchCharacters(): ?int
	{
		return $this->lowFuzzyMatchCharacters;
	}

	public function setLowFuzzyMatchCharacters(int $lowFuzzyMatchCharacters): self
	{
		$this->lowFuzzyMatchCharacters = $lowFuzzyMatchCharacters;

		return $this;
	}

	public function getLowFuzzyMatchSegments(): ?int
	{
		return $this->lowFuzzyMatchSegments;
	}

	public function setLowFuzzyMatchSegments(int $lowFuzzyMatchSegments): self
	{
		$this->lowFuzzyMatchSegments = $lowFuzzyMatchSegments;

		return $this;
	}

	public function getLowFuzzyMatchWords(): ?int
	{
		return $this->lowFuzzyMatchWords;
	}

	public function setLowFuzzyMatchWords(int $lowFuzzyMatchWords): self
	{
		$this->lowFuzzyMatchWords = $lowFuzzyMatchWords;

		return $this;
	}

	public function getLowFuzzyMatchWhitespaces(): ?int
	{
		return $this->lowFuzzyMatchWhitespaces;
	}

	public function setLowFuzzyMatchWhitespaces(int $lowFuzzyMatchWhitespaces): self
	{
		$this->lowFuzzyMatchWhitespaces = $lowFuzzyMatchWhitespaces;

		return $this;
	}

	public function getMediumFuzzyMatchCharacters(): ?int
	{
		return $this->mediumFuzzyMatchCharacters;
	}

	public function setMediumFuzzyMatchCharacters(int $mediumFuzzyMatchCharacters): self
	{
		$this->mediumFuzzyMatchCharacters = $mediumFuzzyMatchCharacters;

		return $this;
	}

	public function getMediumFuzzyMatchSegments(): ?int
	{
		return $this->mediumFuzzyMatchSegments;
	}

	public function setMediumFuzzyMatchSegments(int $mediumFuzzyMatchSegments): self
	{
		$this->mediumFuzzyMatchSegments = $mediumFuzzyMatchSegments;

		return $this;
	}

	public function getMediumFuzzyMatchWords(): ?int
	{
		return $this->mediumFuzzyMatchWords;
	}

	public function setMediumFuzzyMatchWords(int $mediumFuzzyMatchWords): self
	{
		$this->mediumFuzzyMatchWords = $mediumFuzzyMatchWords;

		return $this;
	}

	public function getMediumFuzzyMatchWhitespaces(): ?int
	{
		return $this->mediumFuzzyMatchWhitespaces;
	}

	public function setMediumFuzzyMatchWhitespaces(int $mediumFuzzyMatchWhitespaces): self
	{
		$this->mediumFuzzyMatchWhitespaces = $mediumFuzzyMatchWhitespaces;

		return $this;
	}

	public function getHighFuzzyMatchCharacters(): ?int
	{
		return $this->highFuzzyMatchCharacters;
	}

	public function setHighFuzzyMatchCharacters(int $highFuzzyMatchCharacters): self
	{
		$this->highFuzzyMatchCharacters = $highFuzzyMatchCharacters;

		return $this;
	}

	public function getHighFuzzyMatchSegments(): ?int
	{
		return $this->highFuzzyMatchSegments;
	}

	public function setHighFuzzyMatchSegments(int $highFuzzyMatchSegments): self
	{
		$this->highFuzzyMatchSegments = $highFuzzyMatchSegments;

		return $this;
	}

	public function getHighFuzzyMatchWords(): ?int
	{
		return $this->highFuzzyMatchWords;
	}

	public function setHighFuzzyMatchWords(int $highFuzzyMatchWords): self
	{
		$this->highFuzzyMatchWords = $highFuzzyMatchWords;

		return $this;
	}

	public function getHighFuzzyMatchWhitespaces(): ?int
	{
		return $this->highFuzzyMatchWhitespaces;
	}

	public function setHighFuzzyMatchWhitespaces(int $highFuzzyMatchWhitespaces): self
	{
		$this->highFuzzyMatchWhitespaces = $highFuzzyMatchWhitespaces;

		return $this;
	}

	public function getRepeatsCharacters(): ?int
	{
		return $this->repeatsCharacters;
	}

	public function setRepeatsCharacters(int $repeatsCharacters): self
	{
		$this->repeatsCharacters = $repeatsCharacters;

		return $this;
	}

	public function getRepeatsSegments(): ?int
	{
		return $this->repeatsSegments;
	}

	public function setRepeatsSegments(int $repeatsSegments): self
	{
		$this->repeatsSegments = $repeatsSegments;

		return $this;
	}

	public function getRepeatsWords(): ?int
	{
		return $this->repeatsWords;
	}

	public function setRepeatsWords(int $repeatsWords): self
	{
		$this->repeatsWords = $repeatsWords;

		return $this;
	}

	public function getRepeatsWhitespaces(): ?int
	{
		return $this->repeatsWhitespaces;
	}

	public function setRepeatsWhitespaces(int $repeatsWhitespaces): self
	{
		$this->repeatsWhitespaces = $repeatsWhitespaces;

		return $this;
	}

	public function getLeveragedCharacters(): ?int
	{
		return $this->leveragedCharacters;
	}

	public function setLeveragedCharacters(int $leveragedCharacters): self
	{
		$this->leveragedCharacters = $leveragedCharacters;

		return $this;
	}

	public function getLeveragedSegments(): ?int
	{
		return $this->leveragedSegments;
	}

	public function setLeveragedSegments(int $leveragedSegments): self
	{
		$this->leveragedSegments = $leveragedSegments;

		return $this;
	}

	public function getLeveragedWords(): ?int
	{
		return $this->leveragedWords;
	}

	public function setLeveragedWords(int $leveragedWords): self
	{
		$this->leveragedWords = $leveragedWords;

		return $this;
	}

	public function getLeveragedWhitespaces(): ?int
	{
		return $this->leveragedWhitespaces;
	}

	public function setLeveragedWhitespaces(int $leveragedWhitespaces): self
	{
		$this->leveragedWhitespaces = $leveragedWhitespaces;

		return $this;
	}

	public function getLowFuzzyRepeatsCharacters(): ?int
	{
		return $this->lowFuzzyRepeatsCharacters;
	}

	public function setLowFuzzyRepeatsCharacters(int $lowFuzzyRepeatsCharacters): self
	{
		$this->lowFuzzyRepeatsCharacters = $lowFuzzyRepeatsCharacters;

		return $this;
	}

	public function getLowFuzzyRepeatsSegments(): ?int
	{
		return $this->lowFuzzyRepeatsSegments;
	}

	public function setLowFuzzyRepeatsSegments(int $lowFuzzyRepeatsSegments): self
	{
		$this->lowFuzzyRepeatsSegments = $lowFuzzyRepeatsSegments;

		return $this;
	}

	public function getLowFuzzyRepeatsWords(): ?int
	{
		return $this->lowFuzzyRepeatsWords;
	}

	public function setLowFuzzyRepeatsWords(int $lowFuzzyRepeatsWords): self
	{
		$this->lowFuzzyRepeatsWords = $lowFuzzyRepeatsWords;

		return $this;
	}

	public function getLowFuzzyRepeatsWhitespaces(): ?int
	{
		return $this->lowFuzzyRepeatsWhitespaces;
	}

	public function setLowFuzzyRepeatsWhitespaces(int $lowFuzzyRepeatsWhitespaces): self
	{
		$this->lowFuzzyRepeatsWhitespaces = $lowFuzzyRepeatsWhitespaces;

		return $this;
	}

	public function getMediumFuzzyRepeatsCharacters(): ?int
	{
		return $this->mediumFuzzyRepeatsCharacters;
	}

	public function setMediumFuzzyRepeatsCharacters(int $mediumFuzzyRepeatsCharacters): self
	{
		$this->mediumFuzzyRepeatsCharacters = $mediumFuzzyRepeatsCharacters;

		return $this;
	}

	public function getMediumFuzzyRepeatsSegments(): ?int
	{
		return $this->mediumFuzzyRepeatsSegments;
	}

	public function setMediumFuzzyRepeatsSegments(int $mediumFuzzyRepeatsSegments): self
	{
		$this->mediumFuzzyRepeatsSegments = $mediumFuzzyRepeatsSegments;

		return $this;
	}

	public function getMediumFuzzyRepeatsWords(): ?int
	{
		return $this->mediumFuzzyRepeatsWords;
	}

	public function setMediumFuzzyRepeatsWords(int $mediumFuzzyRepeatsWords): self
	{
		$this->mediumFuzzyRepeatsWords = $mediumFuzzyRepeatsWords;

		return $this;
	}

	public function getMediumFuzzyRepeatsWhitespaces(): ?int
	{
		return $this->mediumFuzzyRepeatsWhitespaces;
	}

	public function setMediumFuzzyRepeatsWhitespaces(int $mediumFuzzyRepeatsWhitespaces): self
	{
		$this->mediumFuzzyRepeatsWhitespaces = $mediumFuzzyRepeatsWhitespaces;

		return $this;
	}

	public function getHighFuzzyRepeatsCharacters(): ?int
	{
		return $this->highFuzzyRepeatsCharacters;
	}

	public function setHighFuzzyRepeatsCharacters(int $highFuzzyRepeatsCharacters): self
	{
		$this->highFuzzyRepeatsCharacters = $highFuzzyRepeatsCharacters;

		return $this;
	}

	public function getHighFuzzyRepeatsSegments(): ?int
	{
		return $this->highFuzzyRepeatsSegments;
	}

	public function setHighFuzzyRepeatsSegments(int $highFuzzyRepeatsSegments): self
	{
		$this->highFuzzyRepeatsSegments = $highFuzzyRepeatsSegments;

		return $this;
	}

	public function getHighFuzzyRepeatsWords(): ?int
	{
		return $this->highFuzzyRepeatsWords;
	}

	public function setHighFuzzyRepeatsWords(int $highFuzzyRepeatsWords): self
	{
		$this->highFuzzyRepeatsWords = $highFuzzyRepeatsWords;

		return $this;
	}

	public function getHighFuzzyRepeatsWhitespaces(): ?int
	{
		return $this->highFuzzyRepeatsWhitespaces;
	}

	public function setHighFuzzyRepeatsWhitespaces(int $highFuzzyRepeatsWhitespaces): self
	{
		$this->highFuzzyRepeatsWhitespaces = $highFuzzyRepeatsWhitespaces;

		return $this;
	}

	public function getNonTranslatableCharacters(): ?int
	{
		return $this->nonTranslatableCharacters;
	}

	public function setNonTranslatableCharacters(int $nonTranslatableCharacters): self
	{
		$this->nonTranslatableCharacters = $nonTranslatableCharacters;

		return $this;
	}

	public function getNonTranslatableSegments(): ?int
	{
		return $this->nonTranslatableSegments;
	}

	public function setNonTranslatableSegments(int $nonTranslatableSegments): self
	{
		$this->nonTranslatableSegments = $nonTranslatableSegments;

		return $this;
	}

	public function getNonTranslatableWords(): ?int
	{
		return $this->nonTranslatableWords;
	}

	public function setNonTranslatableWords(int $nonTranslatableWords): self
	{
		$this->nonTranslatableWords = $nonTranslatableWords;

		return $this;
	}

	public function getNonTranslatableWhitespaces(): ?int
	{
		return $this->nonTranslatableWhitespaces;
	}

	public function setNonTranslatableWhitespaces(int $nonTranslatableWhitespaces): self
	{
		$this->nonTranslatableWhitespaces = $nonTranslatableWhitespaces;

		return $this;
	}

	public function getTotalCharacters(): ?int
	{
		return $this->totalCharacters;
	}

	public function setTotalCharacters(int $totalCharacters): self
	{
		$this->totalCharacters = $totalCharacters;

		return $this;
	}

	public function getTotalSegments(): ?int
	{
		return $this->totalSegments;
	}

	public function setTotalSegments(int $totalSegments): self
	{
		$this->totalSegments = $totalSegments;

		return $this;
	}

	public function getTotalWords(): ?int
	{
		return $this->totalWords;
	}

	public function setTotalWords(int $totalWords): self
	{
		$this->totalWords = $totalWords;

		return $this;
	}

	public function getTotalWhitespaces(): ?int
	{
		return $this->totalWhitespaces;
	}

	public function setTotalWhitespaces(int $totalWhitespaces): self
	{
		$this->totalWhitespaces = $totalWhitespaces;

		return $this;
	}

	public function getMachineTranslationCharacters(): ?int
	{
		return $this->machineTranslationCharacters;
	}

	public function setMachineTranslationCharacters(int $machineTranslationCharacters): self
	{
		$this->machineTranslationCharacters = $machineTranslationCharacters;

		return $this;
	}

	public function getMachineTranslationSegments(): ?int
	{
		return $this->machineTranslationSegments;
	}

	public function setMachineTranslationSegments(int $machineTranslationSegments): self
	{
		$this->machineTranslationSegments = $machineTranslationSegments;

		return $this;
	}

	public function getMachineTranslationWords(): ?int
	{
		return $this->machineTranslationWords;
	}

	public function setMachineTranslationWords(int $machineTranslationWords): self
	{
		$this->machineTranslationWords = $machineTranslationWords;

		return $this;
	}

	public function getMachineTranslationWhitespaces(): ?int
	{
		return $this->machineTranslationWhitespaces;
	}

	public function setMachineTranslationWhitespaces(int $machineTranslationWhitespaces): self
	{
		$this->machineTranslationWhitespaces = $machineTranslationWhitespaces;

		return $this;
	}

	public function getNoMatchCharacters(): ?int
	{
		return $this->noMatchCharacters;
	}

	public function setNoMatchCharacters(int $noMatchCharacters): self
	{
		$this->noMatchCharacters = $noMatchCharacters;

		return $this;
	}

	public function getNoMatchSegments(): ?int
	{
		return $this->noMatchSegments;
	}

	public function setNoMatchSegments(int $noMatchSegments): self
	{
		$this->noMatchSegments = $noMatchSegments;

		return $this;
	}

	public function getNoMatchWords(): ?int
	{
		return $this->noMatchWords;
	}

	public function setNoMatchWords(int $noMatchWords): self
	{
		$this->noMatchWords = $noMatchWords;

		return $this;
	}

	public function getNoMatchWhitespaces(): ?int
	{
		return $this->noMatchWhitespaces;
	}

	public function setNoMatchWhitespaces(int $noMatchWhitespaces): self
	{
		$this->noMatchWhitespaces = $noMatchWhitespaces;

		return $this;
	}

	public function getAnalyticsProject(): ?AnalyticsProject
	{
		return $this->analyticsProject;
	}

	public function setAnalyticsProject(?AnalyticsProject $analyticsProject): self
	{
		$this->analyticsProject = $analyticsProject;

		return $this;
	}
}
