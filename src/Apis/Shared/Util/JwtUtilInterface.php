<?php

namespace App\Apis\Shared\Util;

interface JwtUtilInterface
{
	public function encode(array $tokenData): string;

	public function decode(string $tokenString): \stdClass;
}
