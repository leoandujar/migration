<?php

namespace App\Apis\Shared\DTO;

class SettingInvoiceDto
{
	public ?bool $onlinePayment;

	public function setOnlinePayment(bool $onlinePayment): self
	{
		$this->onlinePayment = $onlinePayment;

		return $this;
	}
}
