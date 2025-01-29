<?php

namespace App\MessageHandler;

use App\Message\LogHelloMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\LoggerService;

#[AsMessageHandler]
final class LogHelloMessageHandler
{
	private LoggerService $loggerSrv;

	public function __construct(LoggerService $loggerSrv)
	{
		$this->loggerSrv = $loggerSrv;
	}

	public function __invoke(LogHelloMessage $message): void
	{
		$this->loggerSrv->addInfo('🎸 '.$message->length);
	}
}
