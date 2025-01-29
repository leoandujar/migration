<?php

namespace App\Command\Services;

use App\Model\Entity\AVParameter;
use App\Model\Repository\AVParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoggerService;
use Symfony\Component\Console\Output\OutputInterface;

class TriggerSyncService
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private AVParameterRepository $parameterRepo;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		AVParameterRepository $parameterRepo,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->parameterRepo = $parameterRepo;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function syncTrigger(OutputInterface $output, string $synFuncName, int $start, int $perpage): void
	{
		do {
			$output->writeln('PROCESSING TRIGGER QUERIES.');
			$paramObj = $this->parameterRepo->findOneBy(['scope' => AVParameter::TYPE_SYNC_FUNCTION_DB, 'name' => $synFuncName]);
			if (!$paramObj) {
				$msg = "No function with name $synFuncName was found.";
				$output->writeln($msg);
				$this->loggerSrv->addWarning($msg);

				return;
			}
			$output->writeln("Query $synFuncName found. Starting parsing.");
			try {
				$sql = trim($paramObj->getValue());
				$stmt = $this->em->getConnection()->prepare($sql);
				$stmt->executeQuery();
				$output->writeln("Query $synFuncName run successfully.");
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error running sync func', $thr);
				$output->writeln($thr->getMessage());
			}
		} while (0);
	}
}
