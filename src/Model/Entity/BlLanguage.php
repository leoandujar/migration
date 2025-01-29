<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_language')]
#[ORM\Index(name: '', columns: ['bllanguage_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['bllanguage_id'])]
#[ORM\Entity]
class BlLanguage implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_languages_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_language_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'bllanguage_id', type: 'bigint', nullable: false)]
	private string $blLanguageId;

	#[ORM\Column(name: 'enabled', type: 'boolean', nullable: true)]
	private ?bool $enabled;

	#[ORM\Column(name: 'english_name', type: 'string', nullable: false)]
	private string $englishName;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'code', type: 'string', nullable: false)]
	private string $code;

	#[ORM\ManyToOne(targetEntity: XtrfLanguage::class)]
	#[ORM\JoinColumn(name: 'xtrf_language_id', referencedColumnName: 'xtrf_language_id', nullable: true)]
	private ?XtrfLanguage $xtrfLanguage;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getBlLanguageId(): ?string
	{
		return $this->blLanguageId;
	}

	public function setBlLanguageId(string $blLanguageId): self
	{
		$this->blLanguageId = $blLanguageId;

		return $this;
	}

	public function getEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(?bool $enabled): self
	{
		$this->enabled = $enabled;

		return $this;
	}

	public function getEnglishName(): ?string
	{
		return $this->englishName;
	}

	public function setEnglishName(string $englishName): self
	{
		$this->englishName = $englishName;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(string $code): self
	{
		$this->code = $code;

		return $this;
	}

	public function getXtrfLanguage(): ?XtrfLanguage
	{
		return $this->xtrfLanguage;
	}

	public function setXtrfLanguage(?XtrfLanguage $xtrfLanguage): self
	{
		$this->xtrfLanguage = $xtrfLanguage;

		return $this;
	}
}
