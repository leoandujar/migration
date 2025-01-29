<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use App\Command\Services\CommandProcessQueueService;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

class CommandsProcessCommand extends Command
{
    use LockableTrait;

    public $hidden = true;
    private LoggerService $loggerSrv;
    private CommandProcessQueueService $commandQueueSrv;

    public function __construct(
        LoggerService $loggerSrv,
        CommandProcessQueueService $commandQueueSrv
    ) {
        parent::__construct();
        $this->loggerSrv = $loggerSrv;
        $this->loggerSrv->setSubcontext(self::class);
        $this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
        $this->commandQueueSrv = $commandQueueSrv;
    }

    protected function configure(): void
    {
        $this
            ->setName('commands:process')
            ->setDescription('Commands: process all commands into the queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }

        $this->commandQueueSrv->dequeueAndProcess($output);

        return Command::SUCCESS;
    }
}
