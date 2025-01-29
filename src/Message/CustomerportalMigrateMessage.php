<?php

namespace App\Message;

final class CustomerportalMigrateMessage
{
	private string $scope;
	private string $customer;

	public function __construct(string $scope, string $customer)
	{
		$this->scope = $scope;
		$this->customer = $customer;
	}

	public function getScope(): string
	{
		return $this->scope;
	}

	public function getCustomer(): string
	{
		return $this->customer;
	}
}
