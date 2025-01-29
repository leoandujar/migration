<?php

namespace App\Connector\Xtrf\Dto;

class PersonContactDto
{
	public ?array $phones = [];
	public ?string $sms;
	public ?string $fax;
	public ?PersonContactEmailDto $emails;

	public function setPhones(array $phones): self
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

	public function setEmails(PersonContactEmailDto $emails): self
	{
		$this->emails = $emails;

		return $this;
	}
}
