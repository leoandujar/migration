<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'xtm_statistics')]
#[ORM\Entity]
class XtmStatistics implements EntityInterface
{
	public const T_SOURCE = 1;
	public const T_TARGET = 2;

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'xtm_statistics_id', type: 'guid')]
	private string $id;

	#[ORM\Column(type: 'smallint')]
	private int $type;

	#[ORM\ManyToOne(targetEntity: AnalyticsProjectStep::class, inversedBy: 'statistics')]
	#[ORM\JoinColumn(name: 'step', referencedColumnName: 'id', nullable: false)]
	private AnalyticsProjectStep $step;

	#[ORM\Column(name: 'high_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $highFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'high_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $highFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'high_fuzzy_match_tracked_time', type: 'integer', nullable: false)]
	private int $highFuzzyMatchTrackedTime = 0;

	#[ORM\Column(name: 'high_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $highFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'high_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $highFuzzyMatchWords = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_tracked_time', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsTrackedTime = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'high_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $highFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'ice_match_characters', type: 'integer', nullable: false)]
	private int $iceMatchCharacters = 0;

	#[ORM\Column(name: 'ice_match_segments', type: 'integer', nullable: false)]
	private int $iceMatchSegments = 0;

	#[ORM\Column(name: 'ice_match_tracked_time', type: 'integer', nullable: false)]
	private int $iceMatchTrackedTime = 0;

	#[ORM\Column(name: 'ice_match_whitespaces', type: 'integer', nullable: false)]
	private int $iceMatchWhitespaces = 0;

	#[ORM\Column(name: 'ice_match_words', type: 'integer', nullable: false)]
	private int $iceMatchWords = 0;

	#[ORM\Column(name: 'leveraged_characters', type: 'integer', nullable: false)]
	private int $leveragedCharacters = 0;

	#[ORM\Column(name: 'leveraged_segments', type: 'integer', nullable: false)]
	private int $leveragedSegments = 0;

	#[ORM\Column(name: 'leveraged_tracked_time', type: 'integer', nullable: false)]
	private int $leveragedTrackedTime = 0;

	#[ORM\Column(name: 'leveraged_whitespaces', type: 'integer', nullable: false)]
	private int $leveragedWhitespaces = 0;

	#[ORM\Column(name: 'leveraged_words', type: 'integer', nullable: false)]
	private int $leveragedWords = 0;

	#[ORM\Column(name: 'low_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'low_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'low_fuzzy_match_tracked_time', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchTrackedTime = 0;

	#[ORM\Column(name: 'low_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'low_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $lowFuzzyMatchWords = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_tracked_time', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsTrackedTime = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'low_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $lowFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'machine_translation_characters', type: 'integer', nullable: false)]
	private int $machineTranslationCharacters = 0;

	#[ORM\Column(name: 'machine_translation_segments', type: 'integer', nullable: false)]
	private int $machineTranslationSegments = 0;

	#[ORM\Column(name: 'machine_translation_tracked_time', type: 'integer', nullable: false)]
	private int $machineTranslationTrackedTime = 0;

	#[ORM\Column(name: 'machine_translation_whitespaces', type: 'integer', nullable: false)]
	private int $machineTranslationWhitespaces = 0;

	#[ORM\Column(name: 'machine_translation_words', type: 'integer', nullable: false)]
	private int $machineTranslationWords = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_characters', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchCharacters = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_segments', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchSegments = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_tracked_time', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchTrackedTime = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_whitespaces', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchWhitespaces = 0;

	#[ORM\Column(name: 'medium_fuzzy_match_words', type: 'integer', nullable: false)]
	private int $mediumFuzzyMatchWords = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_characters', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsCharacters = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_segments', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsSegments = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_tracked_time', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsTrackedTime = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_whitespaces', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsWhitespaces = 0;

	#[ORM\Column(name: 'medium_fuzzy_repeats_words', type: 'integer', nullable: false)]
	private int $mediumFuzzyRepeatsWords = 0;

	#[ORM\Column(name: 'no_matching_characters', type: 'integer', nullable: false)]
	private int $noMatchingCharacters = 0;

	#[ORM\Column(name: 'no_matching_segments', type: 'integer', nullable: false)]
	private int $noMatchingSegments = 0;

	#[ORM\Column(name: 'no_matching_tracked_time', type: 'integer', nullable: false)]
	private int $noMatchingTrackedTime = 0;

	#[ORM\Column(name: 'no_matching_whitespaces', type: 'integer', nullable: false)]
	private int $noMatchingWhitespaces = 0;

	#[ORM\Column(name: 'no_matching_words', type: 'integer', nullable: false)]
	private int $noMatchingWords = 0;

	#[ORM\Column(name: 'non_translatable_characters', type: 'integer', nullable: false)]
	private int $nonTranslatableCharacters = 0;

	#[ORM\Column(name: 'non_translatable_segments', type: 'integer', nullable: false)]
	private int $nonTranslatableSegments = 0;

	#[ORM\Column(name: 'non_translatable_tracked_time', type: 'integer', nullable: false)]
	private int $nonTranslatableTrackedTime = 0;

	#[ORM\Column(name: 'non_translatable_whitespaces', type: 'integer', nullable: false)]
	private int $nonTranslatableWhitespaces = 0;

	#[ORM\Column(name: 'non_translatable_words', type: 'integer', nullable: false)]
	private int $nonTranslatableWords = 0;

	#[ORM\Column(name: 'other_non_translatable_characters', type: 'integer', nullable: false)]
	private int $otherNonTranslatableCharacters = 0;

	#[ORM\Column(name: 'other_non_translatable_tracked_time', type: 'integer', nullable: false)]
	private int $otherNonTranslatableTrackedTime = 0;

	#[ORM\Column(name: 'other_non_translatable_whitespaces', type: 'integer', nullable: false)]
	private int $otherNonTranslatableWhitespaces = 0;

	#[ORM\Column(name: 'other_non_translatable_words', type: 'integer', nullable: false)]
	private int $otherNonTranslatableWords = 0;

	#[ORM\Column(name: 'repeats_characters', type: 'integer', nullable: false)]
	private int $repeatsCharacters = 0;

	#[ORM\Column(name: 'repeats_segments', type: 'integer', nullable: false)]
	private int $repeatsSegments = 0;

	#[ORM\Column(name: 'repeats_tracked_time', type: 'integer', nullable: false)]
	private int $repeatsTrackedTime = 0;

	#[ORM\Column(name: 'repeats_whitespaces', type: 'integer', nullable: false)]
	private int $repeatsWhitespaces = 0;

	#[ORM\Column(name: 'repeats_words', type: 'integer', nullable: false)]
	private int $repeatsWords = 0;

	#[ORM\Column(name: 'total_characters', type: 'integer', nullable: false)]
	private int $totalCharacters = 0;

	#[ORM\Column(name: 'total_segments', type: 'integer', nullable: false)]
	private int $totalSegments = 0;

	#[ORM\Column(name: 'total_time', type: 'integer', nullable: false)]
	private int $totalTime = 0;

	#[ORM\Column(name: 'total_whitespaces', type: 'integer', nullable: false)]
	private int $totalWhitespaces = 0;

	#[ORM\Column(name: 'total_words', type: 'integer', nullable: false)]
	private int $totalWords = 0;

	public function __construct()
	{
		$this->id = Uuid::v4()->__toString();
	}

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

	public function getHighFuzzyMatchTrackedTime(): ?int
	{
		return $this->highFuzzyMatchTrackedTime;
	}

	public function setHighFuzzyMatchTrackedTime(int $highFuzzyMatchTrackedTime): self
	{
		$this->highFuzzyMatchTrackedTime = $highFuzzyMatchTrackedTime;

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

	public function getHighFuzzyMatchWords(): ?int
	{
		return $this->highFuzzyMatchWords;
	}

	public function setHighFuzzyMatchWords(int $highFuzzyMatchWords): self
	{
		$this->highFuzzyMatchWords = $highFuzzyMatchWords;

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

	public function getHighFuzzyRepeatsTrackedTime(): ?int
	{
		return $this->highFuzzyRepeatsTrackedTime;
	}

	public function setHighFuzzyRepeatsTrackedTime(int $highFuzzyRepeatsTrackedTime): self
	{
		$this->highFuzzyRepeatsTrackedTime = $highFuzzyRepeatsTrackedTime;

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

	public function getHighFuzzyRepeatsWords(): ?int
	{
		return $this->highFuzzyRepeatsWords;
	}

	public function setHighFuzzyRepeatsWords(int $highFuzzyRepeatsWords): self
	{
		$this->highFuzzyRepeatsWords = $highFuzzyRepeatsWords;

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

	public function getIceMatchTrackedTime(): ?int
	{
		return $this->iceMatchTrackedTime;
	}

	public function setIceMatchTrackedTime(int $iceMatchTrackedTime): self
	{
		$this->iceMatchTrackedTime = $iceMatchTrackedTime;

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

	public function getIceMatchWords(): ?int
	{
		return $this->iceMatchWords;
	}

	public function setIceMatchWords(int $iceMatchWords): self
	{
		$this->iceMatchWords = $iceMatchWords;

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

	public function getLeveragedTrackedTime(): ?int
	{
		return $this->leveragedTrackedTime;
	}

	public function setLeveragedTrackedTime(int $leveragedTrackedTime): self
	{
		$this->leveragedTrackedTime = $leveragedTrackedTime;

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

	public function getLeveragedWords(): ?int
	{
		return $this->leveragedWords;
	}

	public function setLeveragedWords(int $leveragedWords): self
	{
		$this->leveragedWords = $leveragedWords;

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

	public function getLowFuzzyMatchTrackedTime(): ?int
	{
		return $this->lowFuzzyMatchTrackedTime;
	}

	public function setLowFuzzyMatchTrackedTime(int $lowFuzzyMatchTrackedTime): self
	{
		$this->lowFuzzyMatchTrackedTime = $lowFuzzyMatchTrackedTime;

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

	public function getLowFuzzyMatchWords(): ?int
	{
		return $this->lowFuzzyMatchWords;
	}

	public function setLowFuzzyMatchWords(int $lowFuzzyMatchWords): self
	{
		$this->lowFuzzyMatchWords = $lowFuzzyMatchWords;

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

	public function getLowFuzzyRepeatsTrackedTime(): ?int
	{
		return $this->lowFuzzyRepeatsTrackedTime;
	}

	public function setLowFuzzyRepeatsTrackedTime(int $lowFuzzyRepeatsTrackedTime): self
	{
		$this->lowFuzzyRepeatsTrackedTime = $lowFuzzyRepeatsTrackedTime;

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

	public function getLowFuzzyRepeatsWords(): ?int
	{
		return $this->lowFuzzyRepeatsWords;
	}

	public function setLowFuzzyRepeatsWords(int $lowFuzzyRepeatsWords): self
	{
		$this->lowFuzzyRepeatsWords = $lowFuzzyRepeatsWords;

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

	public function getMachineTranslationTrackedTime(): ?int
	{
		return $this->machineTranslationTrackedTime;
	}

	public function setMachineTranslationTrackedTime(int $machineTranslationTrackedTime): self
	{
		$this->machineTranslationTrackedTime = $machineTranslationTrackedTime;

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

	public function getMachineTranslationWords(): ?int
	{
		return $this->machineTranslationWords;
	}

	public function setMachineTranslationWords(int $machineTranslationWords): self
	{
		$this->machineTranslationWords = $machineTranslationWords;

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

	public function getMediumFuzzyMatchTrackedTime(): ?int
	{
		return $this->mediumFuzzyMatchTrackedTime;
	}

	public function setMediumFuzzyMatchTrackedTime(int $mediumFuzzyMatchTrackedTime): self
	{
		$this->mediumFuzzyMatchTrackedTime = $mediumFuzzyMatchTrackedTime;

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

	public function getMediumFuzzyMatchWords(): ?int
	{
		return $this->mediumFuzzyMatchWords;
	}

	public function setMediumFuzzyMatchWords(int $mediumFuzzyMatchWords): self
	{
		$this->mediumFuzzyMatchWords = $mediumFuzzyMatchWords;

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

	public function getMediumFuzzyRepeatsTrackedTime(): ?int
	{
		return $this->mediumFuzzyRepeatsTrackedTime;
	}

	public function setMediumFuzzyRepeatsTrackedTime(int $mediumFuzzyRepeatsTrackedTime): self
	{
		$this->mediumFuzzyRepeatsTrackedTime = $mediumFuzzyRepeatsTrackedTime;

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

	public function getMediumFuzzyRepeatsWords(): ?int
	{
		return $this->mediumFuzzyRepeatsWords;
	}

	public function setMediumFuzzyRepeatsWords(int $mediumFuzzyRepeatsWords): self
	{
		$this->mediumFuzzyRepeatsWords = $mediumFuzzyRepeatsWords;

		return $this;
	}

	public function getNoMatchingCharacters(): ?int
	{
		return $this->noMatchingCharacters;
	}

	public function setNoMatchingCharacters(int $noMatchingCharacters): self
	{
		$this->noMatchingCharacters = $noMatchingCharacters;

		return $this;
	}

	public function getNoMatchingSegments(): ?int
	{
		return $this->noMatchingSegments;
	}

	public function setNoMatchingSegments(int $noMatchingSegments): self
	{
		$this->noMatchingSegments = $noMatchingSegments;

		return $this;
	}

	public function getNoMatchingTrackedTime(): ?int
	{
		return $this->noMatchingTrackedTime;
	}

	public function setNoMatchingTrackedTime(int $noMatchingTrackedTime): self
	{
		$this->noMatchingTrackedTime = $noMatchingTrackedTime;

		return $this;
	}

	public function getNoMatchingWhitespaces(): ?int
	{
		return $this->noMatchingWhitespaces;
	}

	public function setNoMatchingWhitespaces(int $noMatchingWhitespaces): self
	{
		$this->noMatchingWhitespaces = $noMatchingWhitespaces;

		return $this;
	}

	public function getNoMatchingWords(): ?int
	{
		return $this->noMatchingWords;
	}

	public function setNoMatchingWords(int $noMatchingWords): self
	{
		$this->noMatchingWords = $noMatchingWords;

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

	public function getNonTranslatableTrackedTime(): ?int
	{
		return $this->nonTranslatableTrackedTime;
	}

	public function setNonTranslatableTrackedTime(int $nonTranslatableTrackedTime): self
	{
		$this->nonTranslatableTrackedTime = $nonTranslatableTrackedTime;

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

	public function getNonTranslatableWords(): ?int
	{
		return $this->nonTranslatableWords;
	}

	public function setNonTranslatableWords(int $nonTranslatableWords): self
	{
		$this->nonTranslatableWords = $nonTranslatableWords;

		return $this;
	}

	public function getOtherNonTranslatableCharacters(): ?int
	{
		return $this->otherNonTranslatableCharacters;
	}

	public function setOtherNonTranslatableCharacters(int $otherNonTranslatableCharacters): self
	{
		$this->otherNonTranslatableCharacters = $otherNonTranslatableCharacters;

		return $this;
	}

	public function getOtherNonTranslatableTrackedTime(): ?int
	{
		return $this->otherNonTranslatableTrackedTime;
	}

	public function setOtherNonTranslatableTrackedTime(int $otherNonTranslatableTrackedTime): self
	{
		$this->otherNonTranslatableTrackedTime = $otherNonTranslatableTrackedTime;

		return $this;
	}

	public function getOtherNonTranslatableWhitespaces(): ?int
	{
		return $this->otherNonTranslatableWhitespaces;
	}

	public function setOtherNonTranslatableWhitespaces(int $otherNonTranslatableWhitespaces): self
	{
		$this->otherNonTranslatableWhitespaces = $otherNonTranslatableWhitespaces;

		return $this;
	}

	public function getOtherNonTranslatableWords(): ?int
	{
		return $this->otherNonTranslatableWords;
	}

	public function setOtherNonTranslatableWords(int $otherNonTranslatableWords): self
	{
		$this->otherNonTranslatableWords = $otherNonTranslatableWords;

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

	public function getRepeatsTrackedTime(): ?int
	{
		return $this->repeatsTrackedTime;
	}

	public function setRepeatsTrackedTime(int $repeatsTrackedTime): self
	{
		$this->repeatsTrackedTime = $repeatsTrackedTime;

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

	public function getRepeatsWords(): ?int
	{
		return $this->repeatsWords;
	}

	public function setRepeatsWords(int $repeatsWords): self
	{
		$this->repeatsWords = $repeatsWords;

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

	public function getTotalTime(): ?int
	{
		return $this->totalTime;
	}

	public function setTotalTime(int $totalTime): self
	{
		$this->totalTime = $totalTime;

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

	public function getTotalWords(): ?int
	{
		return $this->totalWords;
	}

	public function setTotalWords(int $totalWords): self
	{
		$this->totalWords = $totalWords;

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
}
