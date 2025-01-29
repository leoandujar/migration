<?php

namespace App\Connector\Xtrf\Response\Users;

use App\Connector\Xtrf\Response\Response;

class GetSingleResponse extends Response
{
	private string $id;
	private string $login;
	private string $firstName;
	private string $lastName;
	private string $email;
	private string $team;
	private string $role;

	public function __construct(int $httpCode, array $rawResponse)
	{
		parent::__construct($httpCode, $rawResponse);

		if ($this->isSuccessfull()) {
			$this->translateRaw();
		}
	}

	public function translateRaw(): void
	{
		$this->id        = $this->raw['id'];
		$this->login     = $this->raw['login'];
		$this->firstName = $this->raw['firstName'];
		$this->lastName  = $this->raw['lastName'];
		$this->email     = $this->raw['email'];
		$this->team      = $this->raw['userGroupName'];
		$this->role      = $this->raw['positionName'];
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getLogin(): ?string
	{
		return $this->login;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function getTeam(): ?string
	{
		return $this->team;
	}

	public function getRole(): ?string
	{
		return $this->role;
	}
}
