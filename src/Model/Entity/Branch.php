<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\SequenceGenerator;
use App\Model\Interfaces\EntityMapperInterface;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Table(name: 'branch')]
#[ORM\Entity]
class Branch implements EntityInterface, EntityMapperInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[SequenceGenerator(sequenceName: 'branch_id_sequence', initialValue: 1)]
    #[ORM\Column(name: 'branch_id', type: 'bigint')]
    private string $id;

    #[ORM\Column(name: 'last_modification_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastModificationDate;

    #[ORM\Column(name: 'version', type: 'integer', nullable: false)]
    private int $version;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: true)]
    private ?bool $active;

    #[ORM\Column(name: 'default_entity', type: 'boolean', nullable: false)]
    private bool $defaultEntity;

    #[ORM\Column(name: 'name', type: 'string', unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'prefered_entity', type: 'boolean', nullable: false)]
    private bool $preferedEntity;

    #[ORM\Column(name: 'correspondence_address', type: 'string', nullable: true)]
    private ?string $correspondenceAddress;

    #[ORM\Column(name: 'correspondence_address_2', type: 'string', nullable: true)]
    private ?string $correspondenceAddress2;

    #[ORM\Column(name: 'correspondence_city', type: 'string', nullable: true)]
    private ?string $correspondenceCity;

    #[ORM\Column(name: 'correspondence_dependent_locality', type: 'string', nullable: true)]
    private ?string $correspondenceDependentLocality;

    #[ORM\Column(name: 'correspondence_sorting_code', type: 'string', nullable: true)]
    private ?string $correspondenceSortingCode;

    #[ORM\Column(name: 'correspondence_zipcode', type: 'string', nullable: true)]
    private ?string $correspondenceZipcode;

    #[ORM\Column(name: 'all_active_payment_methods_available', type: 'boolean', nullable: false)]
    private bool $allActivePaymentMethodsAvailable;

    #[ORM\Column(name: 'country_specific_fiscal_code_value', type: 'string', nullable: true)]
    private ?string $countrySpecificFiscalCodeValue;

    #[ORM\Column(name: 'email', type: 'string', nullable: false)]
    private string $email;

    #[ORM\Column(name: 'fax', type: 'string', nullable: true)]
    private ?string $fax;

    #[ORM\Column(name: 'fullname', type: 'string', nullable: false)]
    private string $fullname;

    #[ORM\Column(name: 'headquarters', type: 'boolean', nullable: false)]
    private bool $headquarters;

    #[ORM\Column(name: 'phone', type: 'string', nullable: true)]
    private ?string $phone;

    #[ORM\Column(name: 'use_system_default_payment_methods', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useSystemDefaultPaymentMethods = true;

    #[ORM\Column(name: 'use_default_logo', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultLogo;

    #[ORM\Column(name: 'use_default_home_portal_background', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultHomePortalBackground;

    #[ORM\Column(name: 'use_default_home_portal_favicon', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultHomePortalFavicon;

    #[ORM\Column(name: 'use_default_vendor_portal_background', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultVendorPortalBackground;

    #[ORM\Column(name: 'use_default_vendor_portal_favicon', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultVendorPortalFavicon;

    #[ORM\Column(name: 'use_default_client_portal_background', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultClientPortalBackground;

    #[ORM\Column(name: 'use_default_client_portal_favicon', type: 'boolean', nullable: false, options: ['default' => 'true'])]
    private bool $useDefaultClientPortalFavicon;

    #[ORM\Column(name: 'home_portal_color_properties', type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $homePortalColorProperties;

    #[ORM\Column(name: 'vendor_portal_color_properties', type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $vendorPortalColorProperties;

    #[ORM\Column(name: 'client_portal_color_properties', type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $clientPortalColorProperties;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(name: 'preferred_currency_id', referencedColumnName: 'xtrf_currency_id', nullable: true)]
    private ?Currency $currency;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'correspondence_country_id', referencedColumnName: 'country_id', nullable: true)]
    private ?Country $correspondenceCountry;

    #[ORM\ManyToOne(targetEntity: Province::class)]
    #[ORM\JoinColumn(name: 'correspondence_province_id', referencedColumnName: 'province_id', nullable: true)]
    private ?Province $correspondenceProvince;

    #[ORM\Column(name: 'www', type: 'string', nullable: true)]
    private ?string $www;

    #[ORM\JoinTable(name: 'branch_available_payment_methods')]
    #[ORM\JoinColumn(name: 'branch_id', referencedColumnName: 'branch_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'payment_method_id', referencedColumnName: 'account_id')]
    #[ORM\ManyToMany(targetEntity: Account::class, cascade: ['persist'], inversedBy: 'paymentsMethods')]
    protected mixed $branchesAvailable;

    #[ORM\JoinTable(name: 'branch_default_payment_methods')]
    #[ORM\JoinColumn(name: 'branch_id', referencedColumnName: 'branch_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'payment_method_id', referencedColumnName: 'account_id')]
    #[ORM\ManyToMany(targetEntity: Account::class, cascade: ['persist'], inversedBy: 'defaultPaymentsMethods')]
    protected mixed $branchesDefault;

    public function __construct()
    {
        $this->branchesAvailable = new ArrayCollection();
        $this->branchesDefault   = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLastModificationDate(): ?\DateTimeInterface
    {
        return $this->lastModificationDate;
    }

    /**
     * @return mixed
     */
    public function setLastModificationDate(?\DateTimeInterface $lastModificationDate): self
    {
        $this->lastModificationDate = $lastModificationDate;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDefaultEntity(): ?bool
    {
        return $this->defaultEntity;
    }

    /**
     * @return mixed
     */
    public function setDefaultEntity(bool $defaultEntity): self
    {
        $this->defaultEntity = $defaultEntity;

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

    public function getPreferedEntity(): ?bool
    {
        return $this->preferedEntity;
    }

    /**
     * @return mixed
     */
    public function setPreferedEntity(bool $preferedEntity): self
    {
        $this->preferedEntity = $preferedEntity;

        return $this;
    }

    public function getCorrespondenceAddress(): ?string
    {
        return $this->correspondenceAddress;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceAddress(?string $correspondenceAddress): self
    {
        $this->correspondenceAddress = $correspondenceAddress;

        return $this;
    }

    public function getCorrespondenceAddress2(): ?string
    {
        return $this->correspondenceAddress2;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceAddress2(?string $correspondenceAddress2): self
    {
        $this->correspondenceAddress2 = $correspondenceAddress2;

        return $this;
    }

    public function getCorrespondenceCity(): ?string
    {
        return $this->correspondenceCity;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceCity(?string $correspondenceCity): self
    {
        $this->correspondenceCity = $correspondenceCity;

        return $this;
    }

    public function getCorrespondenceDependentLocality(): ?string
    {
        return $this->correspondenceDependentLocality;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceDependentLocality(?string $correspondenceDependentLocality): self
    {
        $this->correspondenceDependentLocality = $correspondenceDependentLocality;

        return $this;
    }

    public function getCorrespondenceSortingCode(): ?string
    {
        return $this->correspondenceSortingCode;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceSortingCode(?string $correspondenceSortingCode): self
    {
        $this->correspondenceSortingCode = $correspondenceSortingCode;

        return $this;
    }

    public function getCorrespondenceZipcode(): ?string
    {
        return $this->correspondenceZipcode;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceZipcode(?string $correspondenceZipcode): self
    {
        $this->correspondenceZipcode = $correspondenceZipcode;

        return $this;
    }

    public function getAllActivePaymentMethodsAvailable(): ?bool
    {
        return $this->allActivePaymentMethodsAvailable;
    }

    /**
     * @return mixed
     */
    public function setAllActivePaymentMethodsAvailable(bool $allActivePaymentMethodsAvailable): self
    {
        $this->allActivePaymentMethodsAvailable = $allActivePaymentMethodsAvailable;

        return $this;
    }

    public function getCountrySpecificFiscalCodeValue(): ?string
    {
        return $this->countrySpecificFiscalCodeValue;
    }

    /**
     * @return mixed
     */
    public function setCountrySpecificFiscalCodeValue(?string $countrySpecificFiscalCodeValue): self
    {
        $this->countrySpecificFiscalCodeValue = $countrySpecificFiscalCodeValue;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @return mixed
     */
    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    /**
     * @return mixed
     */
    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getHeadquarters(): ?bool
    {
        return $this->headquarters;
    }

    /**
     * @return mixed
     */
    public function setHeadquarters(bool $headquarters): self
    {
        $this->headquarters = $headquarters;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUseSystemDefaultPaymentMethods(): ?bool
    {
        return $this->useSystemDefaultPaymentMethods;
    }

    /**
     * @return mixed
     */
    public function setUseSystemDefaultPaymentMethods(bool $useSystemDefaultPaymentMethods): self
    {
        $this->useSystemDefaultPaymentMethods = $useSystemDefaultPaymentMethods;

        return $this;
    }

    public function getUseDefaultLogo(): ?bool
    {
        return $this->useDefaultLogo;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultLogo(bool $useDefaultLogo): self
    {
        $this->useDefaultLogo = $useDefaultLogo;

        return $this;
    }

    public function getUseDefaultHomePortalBackground(): ?bool
    {
        return $this->useDefaultHomePortalBackground;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultHomePortalBackground(bool $useDefaultHomePortalBackground): self
    {
        $this->useDefaultHomePortalBackground = $useDefaultHomePortalBackground;

        return $this;
    }

    public function getUseDefaultHomePortalFavicon(): ?bool
    {
        return $this->useDefaultHomePortalFavicon;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultHomePortalFavicon(bool $useDefaultHomePortalFavicon): self
    {
        $this->useDefaultHomePortalFavicon = $useDefaultHomePortalFavicon;

        return $this;
    }

    public function getUseDefaultVendorPortalBackground(): ?bool
    {
        return $this->useDefaultVendorPortalBackground;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultVendorPortalBackground(bool $useDefaultVendorPortalBackground): self
    {
        $this->useDefaultVendorPortalBackground = $useDefaultVendorPortalBackground;

        return $this;
    }

    public function getUseDefaultVendorPortalFavicon(): ?bool
    {
        return $this->useDefaultVendorPortalFavicon;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultVendorPortalFavicon(bool $useDefaultVendorPortalFavicon): self
    {
        $this->useDefaultVendorPortalFavicon = $useDefaultVendorPortalFavicon;

        return $this;
    }

    public function getWww(): ?string
    {
        return $this->www;
    }

    /**
     * @return mixed
     */
    public function setWww(?string $www): self
    {
        $this->www = $www;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCorrespondenceCountry(): ?Country
    {
        return $this->correspondenceCountry;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceCountry(?Country $correspondenceCountry): self
    {
        $this->correspondenceCountry = $correspondenceCountry;

        return $this;
    }

    public function getCorrespondenceProvince(): ?Province
    {
        return $this->correspondenceProvince;
    }

    /**
     * @return mixed
     */
    public function setCorrespondenceProvince(?Province $correspondenceProvince): self
    {
        $this->correspondenceProvince = $correspondenceProvince;

        return $this;
    }

    public function getBranchesAvailable(): Collection
    {
        return $this->branchesAvailable;
    }

    /**
     * @return mixed
     */
    public function addBranchesAvailable(Account $branchesAvailable): self
    {
        if (!$this->branchesAvailable->contains($branchesAvailable)) {
            $this->branchesAvailable[] = $branchesAvailable;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function removeBranchesAvailable(Account $branchesAvailable): self
    {
        if ($this->branchesAvailable->contains($branchesAvailable)) {
            $this->branchesAvailable->removeElement($branchesAvailable);
        }

        return $this;
    }

    public function getBranchesDefault(): Collection
    {
        return $this->branchesDefault;
    }

    /**
     * @return mixed
     */
    public function addBranchesDefault(Account $branchesDefault): self
    {
        if (!$this->branchesDefault->contains($branchesDefault)) {
            $this->branchesDefault[] = $branchesDefault;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function removeBranchesDefault(Account $branchesDefault): self
    {
        if ($this->branchesDefault->contains($branchesDefault)) {
            $this->branchesDefault->removeElement($branchesDefault);
        }

        return $this;
    }

    /**
     *  Generate a hash from attributes.
     */
    public function hashFromObject(): string
    {
        return md5("$this->name");
    }

    /**
     *  Generate a hash from attributes in the remote resource.
     *
     * @param object $remoteSource
     */
    public function hashFromRemote($remoteSource): string
    {
        return md5("$remoteSource->name");
    }

    /**
     *  Update the entity with the data in the remote.
     *
     * @param object $remoteSource
     */
    public function populateFromRemote($remoteSource): void
    {
        $this
            ->setName($remoteSource->name);
    }

    /**
     * @return mixed
     */
    public function getUseDefaultClientPortalBackground(): ?bool
    {
        return $this->useDefaultClientPortalBackground;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultClientPortalBackground(bool $useDefaultClientPortalBackground): self
    {
        $this->useDefaultClientPortalBackground = $useDefaultClientPortalBackground;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUseDefaultClientPortalFavicon(): ?bool
    {
        return $this->useDefaultClientPortalFavicon;
    }

    /**
     * @return mixed
     */
    public function setUseDefaultClientPortalFavicon(bool $useDefaultClientPortalFavicon): self
    {
        $this->useDefaultClientPortalFavicon = $useDefaultClientPortalFavicon;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHomePortalColorProperties(): ?array
    {
        return $this->homePortalColorProperties;
    }

    /**
     * @return mixed
     */
    public function setHomePortalColorProperties(?array $homePortalColorProperties): self
    {
        $this->homePortalColorProperties = $homePortalColorProperties;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVendorPortalColorProperties(): ?array
    {
        return $this->vendorPortalColorProperties;
    }

    /**
     * @return mixed
     */
    public function setVendorPortalColorProperties(?array $vendorPortalColorProperties): self
    {
        $this->vendorPortalColorProperties = $vendorPortalColorProperties;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClientPortalColorProperties(): ?array
    {
        return $this->clientPortalColorProperties;
    }

    /**
     * @return mixed
     */
    public function setClientPortalColorProperties(?array $clientPortalColorProperties): self
    {
        $this->clientPortalColorProperties = $clientPortalColorProperties;

        return $this;
    }
}
