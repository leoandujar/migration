<?php

namespace App\Message;

use Symfony\Component\Console\Output\OutputInterface;

final class CommandsProcessMessage
{
	private bool $isLocked;
	private OutputInterface $output;

	public function __construct(bool $isLocked, OutputInterface $output)
	{
		$this->isLocked = $isLocked;
		$this->output = $output;
	}

	public function getIsLocked(): bool
	{
		return $this->isLocked;
	}

	public function getOutput(): OutputInterface
	{
		return $this->output;
	}
}
