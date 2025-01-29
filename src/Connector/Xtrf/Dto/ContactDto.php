<?php

namespace App\Connector\Xtrf\Dto;

class ContactDto
{
	public ?array $phones = [];
	public ?string $sms;
	public ?string $fax;
	public ?array $emails   = [];
	public ?array $websites = [];

	public function setPhones(?array $phones): self
	{
		$this->phones = $phones;

		return $this;
	}

	public function setSms(?string $sms): self
	{
		$this->sms = $sms;

		return $this;
	}

	public function setFax(?string $fax): self
	{
		$this->fax = $fax;

		return $this;
	}

	public function setEmails(?array $emails): self
	{
		$this->emails = $emails;

		return $this;
	}

	public function setWebsites(?array $websites): self
	{
		$this->websites = $websites;

		return $this;
	}
}
