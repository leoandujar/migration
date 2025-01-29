<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'language')]
#[ORM\Entity]
class Language implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'language_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'iso3', type: 'string', length: 3, unique: true, nullable: false)]
	private string $iso3;

	#[ORM\Column(name: 'iso2b', type: 'string', length: 3, nullable: true, unique: true)]
	private ?string $iso2b;

	#[ORM\Column(name: 'iso2t', type: 'string', length: 3, nullable: true, unique: true)]
	private ?string $iso2t;

	#[ORM\Column(name: 'iso1', type: 'string', length: 2, nullable: true, unique: true)]
	private ?string $iso1;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'scope', type: 'string', length: 50, nullable: false)]
	private string $scope;

	#[ORM\Column(name: 'type', type: 'string', length: 20, nullable: false)]
	private string $type;

	#[ORM\Column(name: 'preferred', type: 'boolean', nullable: false)]
	private bool $preferred;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	public function getIso3(): ?string
	{
		return $this->iso3;
	}

	/**
	 * @return mixed
	 */
	public function setIso3(string $iso3): self
	{
		$this->iso3 = $iso3;

		return $this;
	}

	public function getIso2B(): ?string
	{
		return $this->iso2b;
	}

	/**
	 * @return mixed
	 */
	public function setIso2B(?string $iso2b): self
	{
		$this->iso2b = $iso2b;

		return $this;
	}

	public function getIso2T(): ?string
	{
		return $this->iso2t;
	}

	/**
	 * @return mixed
	 */
	public function setIso2T(?string $iso2t): self
	{
		$this->iso2t = $iso2t;

		return $this;
	}

	public function getIso1(): ?string
	{
		return $this->iso1;
	}

	/**
	 * @return mixed
	 */
	public function setIso1(?string $iso1): self
	{
		$this->iso1 = $iso1;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getScope(): ?string
	{
		return $this->scope;
	}

	/**
	 * @return mixed
	 */
	public function setScope(string $scope): self
	{
		$this->scope = $scope;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getPreferred(): ?bool
	{
		return $this->preferred;
	}

	/**
	 * @return mixed
	 */
	public function setPreferred(bool $preferred): self
	{
		$this->preferred = $preferred;

		return $this;
	}

	public function unChanged($lang): bool
	{
		return $this->iso2b == $lang['Part2B']
		&& $this->iso2t == $lang['Part2T']
		&& $this->iso1 == $lang['Part1']
		&& $this->scope == $lang['Scope']
		&& $this->type == $lang['Language_Type']
		&& $this->name == $lang['Ref_Name'];
	}

	public function populate($lang): self
	{
		$this->setIso2B(empty($lang['Part2B']) ? null : $lang['Part2B'])
			->setIso2T(empty($lang['Part2T']) ? null : $lang['Part2T'])
			->setIso1(empty($lang['Part1']) ? null : $lang['Part1'])
			->setScope($lang['Scope'])
			->setType($lang['Language_Type'])
			->setName($lang['Ref_Name']);

		return $this;
	}

	/**
	 *  Generate a hash from attributes.
	 */
	public function hashFromObject(): string
	{
		return md5("$this->iso2b $this->iso2t $this->iso1 $this->scope $this->type $this->name");
	}

	/**
	 *  Generate a hash from attributes in the remote resource.
	 */
	public function hashFromRemote(array $remoteSource): string
	{
		return md5("{$remoteSource['Part2B']}{$remoteSource['Part2T']}{$remoteSource['Part1']}{$remoteSource['Scope']}{$remoteSource['Language_Type']}{$remoteSource['Ref_Name']}");
	}

	/**
	 *  Update the entity with the data in the remote.
	 */
	public function populateFromRemote(array $remoteSource): void
	{
		$this
			->setIso2B($lang['Part2B'] ?? null)
			->setIso2T($lang['Part2T'] ?? null)
			->setIso1($lang['Part1'] ?? null)
			->setScope($lang['Scope'])
			->setType($lang['Language_Type'])
			->setName($lang['Ref_Name']);
	}
}
