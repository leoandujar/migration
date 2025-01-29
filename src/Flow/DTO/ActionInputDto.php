<?php

namespace App\Flow\DTO;

class ActionInputDto
{
	public function __construct(
		public bool $required,
		public bool $fromAction,
		public string $type,
		public ?array $options = null,
	) {
	}
}
