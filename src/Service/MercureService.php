<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureService
{
	public const STATUS_SUCCESS = 'success';
	public const STATUS_FAILED = 'failed';
	public const TOPIC_FILES = 'files';
	public const TOPIC_COMMANDS = 'commands';
	public HubInterface $hub;

	/**
	 * MercureService constructor.
	 */
	public function __construct(HubInterface $hub)
	{
		$this->hub = $hub;
	}

	public function publish(array $data, string $userId, string $topic = self::TOPIC_FILES): void
	{
		$update = new Update(
			"$topic/$userId",
			json_encode($data),
			true
		);
		$this->hub->publish($update);
	}
}
