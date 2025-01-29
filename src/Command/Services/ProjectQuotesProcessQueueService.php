<?php

namespace App\Command\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProjectQuotesProcessQueueService
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		ParameterBagInterface $parameterBag
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output, ?string $queueName = RedisClients::SESSION_KEY_PROJECTS_QUOTES_NORMAL, $limit = self::LIMIT): void
	{
		do {
			$output->writeln('PROCESSING PROJECTS-QUOTES QUEUE.');
			$dequeueLimit = $limit;
			$runningProcesses = [];
			while ($dequeueLimit-- > 0 && ($payload = $this->redisClients->redisMainDB->lpop($queueName)) !== null) {
				try {
					$output->writeln('Entity found...processing.');
					$projectPath = "{$this->parameterBag->get('kernelProjectDir')}/bin/console";
					$idRedis = uniqid('files_data_');
					$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_PROJECT_QUOTE_PARAMS, [$idRedis => $payload]);
					$process = new Process([
						'php',
						$projectPath,
						'customerportal:files:projects:process',
						"--data=$idRedis",
						"--queue=$queueName",
					], null, null, null, 300);
					try {
						$process->mustRun();
					} catch (ProcessFailedException $ex) {
						$this->loggerSrv->addCritical('Project-Quote process finished unexpectedly. Added to error queue.', $ex);
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_PROJECTS_QUOTES_ERROR, $payload);
					}
					$runningProcesses[] = $process;
					while (count($runningProcesses)) {
						foreach ($runningProcesses as $i => $runningProcess) {
							if (!$runningProcess->isRunning()) {
								$output->writeln('PROCESS END.');
								unset($runningProcesses[$i]);
							}
						}
                        usleep(1000000);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing Project-Quote entity data. Check logs for more details.', $thr);
					if (null !== $payload) {
						$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_PROJECTS_QUOTES, $payload);
					}
					continue;
				}
			}
		} while (0);
	}
}
