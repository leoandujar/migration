<?php

namespace App\Message;

final class LogHelloMessage
{
	public function __construct(public int $length)
	{
	}
}
