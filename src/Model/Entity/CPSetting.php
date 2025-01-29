<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'cp_setting')]
#[ORM\Index(columns: ['cp_setting_id'], name: '')]
#[ORM\Entity]
class CPSetting implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_setting_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_setting_id', type: 'bigint')]
	private string $id;

	#[ORM\OneToOne(inversedBy: 'settings', targetEntity: Customer::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\OneToOne(inversedBy: 'settings', targetEntity: CPSettingProject::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'cp_setting_project_id', referencedColumnName: 'cp_setting_project_id', nullable: true)]
	private ?CPSettingProject $projectSettings;

	#[ORM\OneToOne(inversedBy: 'settings', targetEntity: CPSettingQuote::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'cp_setting_quote_id', referencedColumnName: 'cp_setting_quote_id', nullable: true)]
	private ?CPSettingQuote $quoteSettings;

	#[ORM\OneToOne(inversedBy: 'settings', targetEntity: CPSettingInvoice::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'cp_setting_invoice_id', referencedColumnName: 'cp_setting_invoice_id', nullable: true)]
	private ?CPSettingInvoice $invoiceSettings;

	#[ORM\OneToOne(inversedBy: 'settings', targetEntity: CPSettingReport::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'cp_setting_report_id', referencedColumnName: 'cp_setting_report_id', nullable: true)]
	private ?CPSettingReport $reportSettings;


	#[ORM\Column(name: 'team_webhook', type: 'string', nullable: true)]
	private ?string $teamWebhook;

	public function getId(): string
	{
		return $this->id;
	}

	public function __toString()
	{
		return "{$this->getId()}";
	}

	public function getTeamWebhook(): ?string
	{
		return $this->teamWebhook;
	}

	public function setTeamWebhook(?string $teamWebhook): static
	{
		$this->teamWebhook = $teamWebhook;

		return $this;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(Customer $customer): static
	{
		$this->customer = $customer;

		return $this;
	}

	public function getProjectSettings(): ?CPSettingProject
	{
		return $this->projectSettings;
	}

	public function setProjectSettings(?CPSettingProject $projectSettings): static
	{
		$this->projectSettings = $projectSettings;

		return $this;
	}

	public function getQuoteSettings(): ?CPSettingQuote
	{
		return $this->quoteSettings;
	}

	public function setQuoteSettings(?CPSettingQuote $quoteSettings): static
	{
		$this->quoteSettings = $quoteSettings;

		return $this;
	}

	public function getInvoiceSettings(): ?CPSettingInvoice
	{
		return $this->invoiceSettings;
	}

	public function setInvoiceSettings(?CPSettingInvoice $invoiceSettings): static
	{
		$this->invoiceSettings = $invoiceSettings;

		return $this;
	}

	public function getReportSettings(): ?CPSettingReport
	{
		return $this->reportSettings;
	}

	public function setReportSettings(?CPSettingReport $reportSettings): static
	{
		$this->reportSettings = $reportSettings;

		return $this;
	}
}
