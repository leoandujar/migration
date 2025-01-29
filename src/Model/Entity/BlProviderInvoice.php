<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'bl_provider_invoice')]
#[ORM\Index(name: '', columns: ['blprovider_invoice_id'])]
#[ORM\UniqueConstraint(name: '', columns: ['blprovider_invoice_id'])]
#[ORM\Entity]
class BlProviderInvoice implements EntityInterface
{
	public const STATUS_DRAFT = 'draft';
	public const STATUS_APPROVED = 'approved';
	public const STATUS_PAID = 'paid';
	public const STATUS_VOIDED = 'voided';
	public const STATUS_ARCHIVED = 'archived';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'bl_provider_invoice_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'bl_provider_invoice_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'blprovider_invoice_id', type: 'bigint', nullable: false)]
	private string $blProviderInvoiceId;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'admin_created', type: 'boolean', nullable: true)]
	private ?bool $adminCreated;

	#[ORM\Column(name: 'due_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $dueDate;

	#[ORM\Column(name: 'start_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $startDate;

	#[ORM\Column(name: 'end_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $endDate;

	#[ORM\Column(name: 'invoice_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $invoiceDate;

	#[ORM\Column(name: 'status', type: 'string', length: 50, nullable: true)]
	private ?string $status;

	#[ORM\Column(name: 'number', type: 'string', length: 150, nullable: true)]
	private ?string $number;

	#[ORM\Column(name: 'name', type: 'string', length: 200, nullable: true)]
	private ?string $name;

	#[ORM\Column(name: 'type', type: 'string', length: 200, nullable: true)]
	private ?string $type;

	#[ORM\Column(name: 'number_of_appointments', type: 'integer', nullable: true)]
	private ?int $numberOfAppointments;

	#[ORM\Column(name: 'number_of_calls', type: 'integer', nullable: true)]
	private ?int $numberOfCalls;

	#[ORM\Column(name: 'po_number', type: 'string', length: 200, nullable: true)]
	private ?string $poNumber;

	#[ORM\Column(name: 'revised_count', type: 'integer', nullable: true)]
	private ?int $revisedCount;

	#[ORM\Column(name: 'invoiced_id', type: 'integer', nullable: true)]
	private ?int $invoicedId;

	#[ORM\Column(name: 'total', type: 'decimal', precision: 19, scale: 2, nullable: true)]
	private ?float $total;

	#[ORM\Column(name: 'export_state_id', type: 'string', nullable: true)]
	private ?string $exportStateId;

	#[ORM\Column(name: 'invoice_terms_id', type: 'string', nullable: true)]
	private ?string $invoiceTermsId;

	#[ORM\Column(name: 'quick_books_id', type: 'string', nullable: true)]
	private ?string $quickBooksId;

	#[ORM\Column(name: 'invoiced_image_key', type: 'string', nullable: true)]
	private ?string $invoicedImageKey;

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

	public function getCreatedDate(): ?\DateTimeInterface
	{
		return $this->createdDate;
	}

	public function setCreatedDate(?\DateTimeInterface $createdDate): self
	{
		$this->createdDate = $createdDate;

		return $this;
	}

	public function getDueDate(): ?\DateTimeInterface
	{
		return $this->dueDate;
	}

	public function setDueDate(?\DateTimeInterface $dueDate): self
	{
		$this->dueDate = $dueDate;

		return $this;
	}

	public function getEndDate(): ?\DateTimeInterface
	{
		return $this->endDate;
	}

	public function setEndDate(?\DateTimeInterface $endDate): self
	{
		$this->endDate = $endDate;

		return $this;
	}

	public function getInvoiceDate(): ?\DateTimeInterface
	{
		return $this->invoiceDate;
	}

	public function setInvoiceDate(?\DateTimeInterface $invoiceDate): self
	{
		$this->invoiceDate = $invoiceDate;

		return $this;
	}

	public function getStartDate(): ?\DateTimeInterface
	{
		return $this->startDate;
	}

	public function setStartDate(?\DateTimeInterface $startDate): self
	{
		$this->startDate = $startDate;

		return $this;
	}

	public function getNumber(): ?string
	{
		return $this->number;
	}

	public function setNumber(?string $number): self
	{
		$this->number = $number;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getNumberOfAppointments(): ?int
	{
		return $this->numberOfAppointments;
	}

	public function setNumberOfAppointments(?int $numberOfAppointments): self
	{
		$this->numberOfAppointments = $numberOfAppointments;

		return $this;
	}

	public function getNumberOfCalls(): ?int
	{
		return $this->numberOfCalls;
	}

	public function setNumberOfCalls(?int $numberOfCalls): self
	{
		$this->numberOfCalls = $numberOfCalls;

		return $this;
	}

	public function getPoNumber(): ?string
	{
		return $this->poNumber;
	}

	public function setPoNumber(?string $poNumber): self
	{
		$this->poNumber = $poNumber;

		return $this;
	}

	public function getRevisedCount(): ?int
	{
		return $this->revisedCount;
	}

	public function setRevisedCount(?int $revisedCount): self
	{
		$this->revisedCount = $revisedCount;

		return $this;
	}

	public function getTotal(): ?string
	{
		return $this->total;
	}

	public function setTotal(?string $total): self
	{
		$this->total = $total;

		return $this;
	}

	public function getBlProviderInvoiceId(): ?string
	{
		return $this->blProviderInvoiceId;
	}

	public function setBlProviderInvoiceId(string $blProviderInvoiceId): self
	{
		$this->blProviderInvoiceId = $blProviderInvoiceId;

		return $this;
	}

	public function isAdminCreated(): ?bool
	{
		return $this->adminCreated;
	}

	public function setAdminCreated(?bool $adminCreated): self
	{
		$this->adminCreated = $adminCreated;

		return $this;
	}

	public function getInvoicedId(): ?int
	{
		return $this->invoicedId;
	}

	public function setInvoicedId(?int $invoicedId): self
	{
		$this->invoicedId = $invoicedId;

		return $this;
	}

	public function getExportStateId(): ?string
	{
		return $this->exportStateId;
	}

	public function setExportStateId(?string $exportStateId): self
	{
		$this->exportStateId = $exportStateId;

		return $this;
	}

	public function getInvoiceTermsId(): ?string
	{
		return $this->invoiceTermsId;
	}

	public function setInvoiceTermsId(?string $invoiceTermsId): self
	{
		$this->invoiceTermsId = $invoiceTermsId;

		return $this;
	}

	public function getQuickBooksId(): ?string
	{
		return $this->quickBooksId;
	}

	public function setQuickBooksId(?string $quickBooksId): self
	{
		$this->quickBooksId = $quickBooksId;

		return $this;
	}

	public function getInvoicedImageKey(): ?string
	{
		return $this->invoicedImageKey;
	}

	public function setInvoicedImageKey(?string $invoicedImageKey): self
	{
		$this->invoicedImageKey = $invoicedImageKey;

		return $this;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(?string $status): self
	{
		$this->status = $status;

		return $this;
	}
}
