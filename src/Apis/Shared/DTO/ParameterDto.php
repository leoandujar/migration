<?php

namespace App\Apis\Shared\DTO;

class ParameterDto
{
	public function __construct(
		public ?string $id,
		public ?string $name,
		public ?string $scope,
		public ?string $value
	) {
	}
}
