<?php

namespace App\Flow\DTO;

class ActionOutputDto
{
	public function __construct(
		public string $description,
		public string $type,
	) {
	}
}
