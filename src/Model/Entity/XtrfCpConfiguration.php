<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: 'rest_api_configuration')]
class XtrfCpConfiguration implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(name: 'rest_api_configuration_id', type: 'bigint')]
	private $id;

	#[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
	private $lastModificationDate;

	#[ORM\Column(name: 'version', type: 'integer', nullable: false)]
	private $version;

	#[ORM\Column(type: 'boolean')]
	private $allActiveLanguagesAvailable;

	#[ORM\Column(type: 'boolean')]
	private $allActiveSpecializationsAvailable;

	#[ORM\Column(type: 'string', length: 255)]
	private $contactEmail;

	#[ORM\Column(type: 'boolean')]
	private $enabled;

	#[ORM\Column(type: 'string', length: 255)]
	private $portalUrl;

	#[ORM\Column(type: 'boolean', options: ['default' => 'false'])]
	private $landingCardEnabled;

	#[ORM\Column(type: 'text', nullable: true)]
	private $landingCardTitle;

	#[ORM\Column(type: 'text', nullable: true)]
	private $landingCardContent;

	#[ORM\Column(type: 'boolean', options: ['default' => 'true'])]
	private $allActiveServicesAvailable;

	#[ORM\JoinTable(name: 'rest_api_languages')]
	#[ORM\JoinColumn(name: 'rest_api_configuration_id', referencedColumnName: 'rest_api_configuration_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'language_id', referencedColumnName: 'xtrf_language_id')]
	#[ORM\ManyToMany(targetEntity: XtrfLanguage::class, cascade: ['persist'])]
    #[ORM\OrderBy(["name" => "ASC"])]
	protected mixed $languages;

	#[ORM\JoinTable(name: 'rest_api_services')]
	#[ORM\JoinColumn(name: 'rest_api_configuration_id', referencedColumnName: 'rest_api_configuration_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'service_id', referencedColumnName: 'service_id')]
	#[ORM\ManyToMany(targetEntity: Service::class, cascade: ['persist'])]
	protected mixed $services;

	#[ORM\JoinTable(name: 'rest_api_specializations')]
	#[ORM\JoinColumn(name: 'rest_api_configuration_id', referencedColumnName: 'rest_api_configuration_id', onDelete: 'CASCADE')]
	#[ORM\InverseJoinColumn(name: 'language_specialization_id', referencedColumnName: 'language_specialization_id')]
	#[ORM\ManyToMany(targetEntity: LanguageSpecialization::class, cascade: ['persist'])]
	protected mixed $specializations;

	public function __construct()
	{
		$this->languages = new ArrayCollection();
		$this->services = new ArrayCollection();
		$this->specializations = new ArrayCollection();
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLastModificationDate(): ?\DateTimeInterface
	{
		return $this->lastModificationDate;
	}

	public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
	{
		$this->lastModificationDate = $lastModificationDate;

		return $this;
	}

	public function getVersion(): ?int
	{
		return $this->version;
	}

	public function setVersion(int $version): self
	{
		$this->version = $version;

		return $this;
	}

	public function isAllActiveLanguagesAvailable(): ?bool
	{
		return $this->allActiveLanguagesAvailable;
	}

	public function setAllActiveLanguagesAvailable(bool $allActiveLanguagesAvailable): self
	{
		$this->allActiveLanguagesAvailable = $allActiveLanguagesAvailable;

		return $this;
	}

	public function isAllActiveSpecializationsAvailable(): ?bool
	{
		return $this->allActiveSpecializationsAvailable;
	}

	public function setAllActiveSpecializationsAvailable(bool $allActiveSpecializationsAvailable): self
	{
		$this->allActiveSpecializationsAvailable = $allActiveSpecializationsAvailable;

		return $this;
	}

	public function getContactEmail(): ?string
	{
		return $this->contactEmail;
	}

	public function setContactEmail(string $contactEmail): self
	{
		$this->contactEmail = $contactEmail;

		return $this;
	}

	public function isEnabled(): ?bool
	{
		return $this->enabled;
	}

	public function setEnabled(bool $enabled): self
	{
		$this->enabled = $enabled;

		return $this;
	}

	public function getPortalUrl(): ?string
	{
		return $this->portalUrl;
	}

	public function setPortalUrl(string $portalUrl): self
	{
		$this->portalUrl = $portalUrl;

		return $this;
	}

	public function isLandingCardEnabled(): ?bool
	{
		return $this->landingCardEnabled;
	}

	public function setLandingCardEnabled(bool $landingCardEnabled): self
	{
		$this->landingCardEnabled = $landingCardEnabled;

		return $this;
	}

	public function getLandingCardTitle(): ?string
	{
		return $this->landingCardTitle;
	}

	public function setLandingCardTitle(?string $landingCardTitle): self
	{
		$this->landingCardTitle = $landingCardTitle;

		return $this;
	}

	public function getLandingCardContent(): ?string
	{
		return $this->landingCardContent;
	}

	public function setLandingCardContent(?string $landingCardContent): self
	{
		$this->landingCardContent = $landingCardContent;

		return $this;
	}

	public function isAllActiveServicesAvailable(): ?bool
	{
		return $this->allActiveServicesAvailable;
	}

	public function setAllActiveServicesAvailable(bool $allActiveServicesAvailable): self
	{
		$this->allActiveServicesAvailable = $allActiveServicesAvailable;

		return $this;
	}

	/**
	 * @return Collection<int, XtrfLanguage>
	 */
	public function getLanguages(): Collection
	{
		return $this->languages;
	}

	public function addLanguage(XtrfLanguage $language): self
	{
		if (!$this->languages->contains($language)) {
			$this->languages->add($language);
		}

		return $this;
	}

	public function removeLanguage(XtrfLanguage $language): self
	{
		$this->languages->removeElement($language);

		return $this;
	}

	/**
	 * @return Collection<int, Service>
	 */
	public function getServices(): Collection
	{
		return $this->services;
	}

	public function addService(Service $service): self
	{
		if (!$this->services->contains($service)) {
			$this->services->add($service);
		}

		return $this;
	}

	public function removeService(Service $service): self
	{
		$this->services->removeElement($service);

		return $this;
	}

	/**
	 * @return Collection<int, LanguageSpecialization>
	 */
	public function getSpecializations(): Collection
	{
		return $this->specializations;
	}

	public function addSpecialization(LanguageSpecialization $specialization): self
	{
		if (!$this->specializations->contains($specialization)) {
			$this->specializations->add($specialization);
		}

		return $this;
	}

	public function removeSpecialization(LanguageSpecialization $specialization): self
	{
		$this->specializations->removeElement($specialization);

		return $this;
	}
}
