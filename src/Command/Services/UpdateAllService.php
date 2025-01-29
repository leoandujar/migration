<?php

namespace App\Command\Services;

use App\Service\LoggerService;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAllService
{
	private LoggerService $loggerSrv;
	private array $postCommands = [
		'xtm:projects:link',
		'xtm:jobs:link',
		'xtm:metrics:process',
		'xtm:lqa:process',
		'xtm:statistics:process',
		'xtm:extended:process',
	];

	public function __construct(
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function execute(InputInterface $input, OutputInterface $output): void
	{
		Helper::applyFormatting($output);

		if ($input->getOption('limit')) {
			$limit = ' --limit '.$input->getOption('limit');
		} else {
			$limit = '';
		}

		foreach ($this->postCommands as $command) {
			$process = Process::fromShellCommandline('./bin/console '.$command.' --ansi'.$limit.' 2>&1');
			$process->setTimeout(3600);
			$process->setIdleTimeout(300);
			$process->start();
			$isError = false;
			foreach ($process as $type => $data) {
				$output->write($data);
				if ($process::ERR === $type) {
					$isError = true;
				}
			}
			if ($isError) {
				$output->writeln(['<entname>'.$command.' execution failed</entname>', '']);
				$this->loggerSrv->addInfo('execution failed: '.$command);
			} else {
				$output->writeln(['<entval>'.$command.' execution was successful</entval>', '']);
				$this->loggerSrv->addInfo('execution was successful: '.$command);
			}
		}
	}
}
