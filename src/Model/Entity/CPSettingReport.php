<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'cp_setting_report')]
#[ORM\Index(name: '', columns: ['cp_setting_report_id'])]
#[ORM\Entity]
class CPSettingReport implements EntityInterface
{
	public const DEADLINE_DISABLED = 'disabled';
	public const DEADLINE_PREDICTION = 'prediction';
	public const DEADLINE_PREDEFINED = 'predefined';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_setting_report_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_setting_report_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'predefined_data', type: 'json', nullable: true)]
	private ?array $predefinedData;

	#[ORM\OneToOne(targetEntity: CPSetting::class, mappedBy: 'reportSettings', cascade: ['persist', 'remove'])]
	private CPSetting $settings;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getPredefinedData(): ?array
	{
		return $this->predefinedData;
	}

	public function setPredefinedData(?array $predefinedData): self
	{
		$this->predefinedData = $predefinedData;

		return $this;
	}

	public function getSettings(): ?CPSetting
	{
		return $this->settings;
	}

	public function setSettings(?CPSetting $settings): self
	{
		// unset the owning side of the relation if necessary
		if (null === $settings && null !== $this->settings) {
			$this->settings->setReportSettings(null);
		}

		// set the owning side of the relation if necessary
		if (null !== $settings && $settings->getReportSettings() !== $this) {
			$settings->setReportSettings($this);
		}

		$this->settings = $settings;

		return $this;
	}
}
