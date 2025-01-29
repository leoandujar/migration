<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FilesQueueService
{
	private const LIMIT = 10;

	private LoggerService $loggerSrv;
	private RedisClients $redisClients;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		ParameterBagInterface $parameterBag,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
		$this->parameterBag = $parameterBag;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function dequeueAndProcess(OutputInterface $output): void
	{
		do {
			$output->writeln('PROCESSING FILES QUEUE.');
			$dequeueLimit = self::LIMIT;
			$runningProcesses = [];
			while ($dequeueLimit-- > 0 && ($elementId = $this->redisClients->redisMainDB->zrange(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, 0, 0)) !== null) {
				try {
					$payload = $fileObj = null;
					if (!empty($elementId)) {
						$elementId = array_shift($elementId);
						$this->redisClients->redisMainDB->zrem(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, $elementId);
						$payload = $this->redisClients->redisMainDB->hmget(RedisClients::SESSION_KEY_PENDING_FILES, $elementId);
					}
					if (!$payload) {
						// THIS CODE IS TEMPORAL DUE COMPATIBILITY WITH CURRENT PROD FILES.
						// AFTER ONE WEEK WORKING THIS NEW CODE ON PROD, THIS CAN BE SAFETY REMOVED.
						// ################ START LATER DELETE CODE #########################
						$values = $this->redisClients->redisMainDB->hgetall(RedisClients::SESSION_KEY_PENDING_FILES);
						if (!$values) {
							break;
						}
						if (!count($values)) {
							$output->writeln('Files queue is empty.');
							continue;
						}
						$payload = $values;
						// ############ END LATER DELETE CODE ####################################
						// THIS BREAK SHOULD BE UNCOMMENTED AFTER DELETE PREVIOUS CODE break;
					}
					$payload = array_shift($payload);
					if (null === $payload) {
						$output->writeln('Files queue is empty.');
						continue;
					}
					if (($fileObj = unserialize($payload)) === false) {
						throw new \Exception("Unable to unserialize payload with data $payload");
					}
					$output->writeln('File found...processing.');
					$projectPath = "{$this->parameterBag->get('kernelProjectDir')}/bin/console";
					$data = json_encode($fileObj);
					$process = new Process([
						'php',
						$projectPath,
						'customerportal:files:pending:process',
						"--data=$data",
					], null, null, null, 600);
					try {
						$process->start();
					} catch (ProcessFailedException $ex) {
						if (null !== $fileObj) {
							$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, microtime(true), $fileObj->Key);
						}
						$this->loggerSrv->addCritical('File process finished unexpectedly. Added to queue again.', $ex);
					}
					$runningProcesses[] = $process;
					while (count($runningProcesses)) {
						foreach ($runningProcesses as $i => $runningProcess) {
							if (!$runningProcess->isRunning()) {
								$output->writeln('Entity PROCESSED.');
								unset($runningProcesses[$i]);
							}
						}
						usleep(1000000);
					}
				} catch (\Throwable $thr) {
					$this->loggerSrv->addError('Error processing file data. Check logs for more details.', $thr);
					continue;
				}
			}
		} while (0);
	}
}
