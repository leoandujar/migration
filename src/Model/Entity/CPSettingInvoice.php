<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'cp_setting_invoice')]
#[ORM\Index(name: '', columns: ['cp_setting_invoice_id'])]
#[ORM\Entity]
class CPSettingInvoice implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'cp_setting_invoice_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'cp_setting_invoice_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'online_payment', type: 'boolean', nullable: false, options: ['default' => 'false'])]
	private bool $onlinePayment = false;

	#[ORM\OneToOne(targetEntity: CPSetting::class, mappedBy: 'invoiceSettings', cascade: ['persist', 'remove'])]
	private CPSetting $settings;

	/**
	 * CPSettingInvoice constructor.
	 */
	public function __construct()
	{
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getOnlinePayment(): ?bool
	{
		return $this->onlinePayment;
	}

	/**
	 * @return mixed
	 */
	public function setOnlinePayment(bool $onlinePayment): self
	{
		$this->onlinePayment = $onlinePayment;

		return $this;
	}

	public function getSettings(): ?CPSetting
	{
		return $this->settings;
	}

	/**
	 * @return mixed
	 */
	public function setSettings(?CPSetting $settings): self
	{
		$this->settings = $settings;

		// set (or unset) the owning side of the relation if necessary
		$newInvoiceSettings = null === $settings ? null : $this;
		if ($settings->getInvoiceSettings() !== $newInvoiceSettings) {
			$settings->setInvoiceSettings($newInvoiceSettings);
		}

		return $this;
	}
}
