<?php

namespace App\Connector\Github\Response;

interface ResponseInterface
{
	public static function decode($data): self;
}
