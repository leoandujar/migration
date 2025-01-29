<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use App\Service\MercureService;
use App\Connector\Xtrf\XtrfConnector;
use App\Linker\Services\RedisClients;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerportalFilesPendingProcessCommand extends Command
{
	public $hidden = true;
	private XtrfConnector $xtrfCon;
	private LoggerService $loggerSrv;
	private MercureService $mercureSrv;
	private RedisClients $redisClients;

	public function __construct(
		XtrfConnector $xtrfCon,
		RedisClients $redisClients,
		MercureService $mercureSrv,
		LoggerService $loggerSrv,
	) {
		parent::__construct();
		$this->xtrfCon = $xtrfCon;
		$this->loggerSrv = $loggerSrv;
		$this->mercureSrv = $mercureSrv;
		$this->redisClients = $redisClients;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	protected function configure(): void
	{
		$this
			->setName('customerportal:files:pending:process')
			->addOption(
				'data',
				'd',
				InputOption::VALUE_REQUIRED
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$data = $input->getOption('data');
		if (!$data) {
			$this->loggerSrv->addError('File Process Instance was called with empty data. Aborting.');

			return Command::FAILURE;
		}

		$data = json_decode($data);
		if (null === $data) {
			$this->loggerSrv->addError('Data could not be decoded. Aborting.');

			return Command::FAILURE;
		}
		$this->processEntity($data);

		return Command::SUCCESS;
	}

	private function processEntity($data): void
	{
		try {
			switch ($data->EntityName) {
				case CustomerportalFilesProjectsProcessCommand::TYPE_CP_PROJECT_EXTRA_FILES:
				case CustomerportalFilesProjectsProcessCommand::TYPE_CP_QUOTES_EXTRA_FILES:
					$this->loggerSrv->addInfo("Processing Customer File {$data->EntityName} {$data->FileSize} {$data->Key} {$data->FilePath}", (array) $data);
					$fileContent = file_get_contents($data->FilePath);
					$processParams[] = [
						'name' => 'file',
						'filename' => $data->FileName,
						'contents' => $fileContent,
					];
					$uploadResponse = $this->xtrfCon->uploadProjectFile($processParams);
					if (!$uploadResponse->isSuccessfull()) {
						if (property_exists($data, 'owner')) {
							$this->mercureSrv->publish([
								'fileId' => $data->Key,
								'status' => MercureService::STATUS_FAILED,
								'entityType' => $data->EntityName,
							], $data->owner);
						}
						$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_AWAITING_FILES, $data->Key);
						$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, microtime(true), $data->Key);
						break;
					}
					$data->Token = $uploadResponse->getToken();
					$this->loggerSrv->addInfo("Processed Successfully Customer File $data->EntityName $data->Key $data->FilePath", (array) $data);
					$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_AWAITING_FILES, [$data->Key => serialize($data)]);
					$this->redisClients->redisMainDB->hdel(RedisClients::SESSION_KEY_PENDING_FILES, $data->Key);
					break;
				default:
					$this->loggerSrv->addError("Unrecognized file entity name $data->entityName");
			}
		} catch (\Throwable $thr) {
			if ($data && $data->Key) {
				$this->redisClients->redisMainDB->zadd(RedisClients::SESSION_KEY_PENDING_FILES_ORDER, microtime(true), $data->Key);
			}
			$this->loggerSrv->addError('Error processing file', $thr);
		}
	}
}
