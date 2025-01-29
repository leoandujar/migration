<?php

namespace App\Apis\Shared\Facade;

use App\Linker\Services\RedisClients;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\FileSystem\FileSystemService;
use App\Service\JwtService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppFacade
{
	private static ?AppFacade $instance = null;
	public ?ValidatorInterface $validator = null;
	public ?FileSystemService $fileSystemSrv;
	public ?CloudFileSystemService $fileBucketSrv;
	public ?RedisClients $redisClients;
	public ?JwtService $jwtSrv;

	public function __construct()
	{
	}

	public static function getInstance(): AppFacade
	{
		if (null === self::$instance) {
			self::$instance = new AppFacade();
		}

		return self::$instance;
	}
}
