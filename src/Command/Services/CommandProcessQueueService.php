<?php

namespace App\Command\Services;

use App\Service\MercureService;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CommandProcessQueueService
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private MercureService $mercureSrv;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		MercureService $mercureSrv,
		ParameterBagInterface $parameterBag,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->mercureSrv = $mercureSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output): void
	{
		do {
			$output->writeln('PROCESSING COMMANDS QUEUE.');
			$dequeueLimit = self::LIMIT;
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_COMMANDS_QUEUE)) !== null) {
				try {
					if (($fileObj = unserialize($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}
					$output->writeln('Command found...processing.');
					$projectPath = "{$this->parameterBag->get('kernelProjectDir')}/bin/console";
					$queueId = $fileObj->id;
					$commandCode = $fileObj->code;
					$arguments = $fileObj->arguments ?? '';
					$this->loggerSrv->addInfo('Running command '.$commandCode.' dequeue and starting.');
					$process = new Process(array_merge([
						'php',
						$projectPath,
						$commandCode,
					], explode(' ', $arguments)), null, null, null, 1800);
					try {
						$process->mustRun(function ($type, $buffer) use ($output, $queueId, $fileObj) {
							$output->writeln($buffer);
							$this->mercureSrv->publish([
								'commandId' => $queueId,
								'buffer' => $buffer,
							], $fileObj->owner, MercureService::TOPIC_COMMANDS);
						});
					} catch (ProcessFailedException $ex) {
						$this->loggerSrv->addCritical('Command queue process finished unexpectedly.');
						$this->mercureSrv->publish([
							'commandId' => $queueId,
							'buffer' => $ex->getMessage(),
						], $fileObj->owner, MercureService::TOPIC_COMMANDS);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Command entity data. Check logs for more details.', $thr);
					continue;
				}
			}
		} while (0);
	}
}
