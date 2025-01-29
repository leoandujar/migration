<?php

namespace App\MessageHandler;

use App\Message\ReplicationTriggersSyncMessage;
use App\Model\Entity\AVParameter;
use App\Model\Repository\AVParameterRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ReplicationTriggersSyncMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
	}

	public function __invoke(ReplicationTriggersSyncMessage $message): void
	{
		$synFuncName = $message->getName();
		do {
			$paramObj = $this->em->getRepository(AVParameter::class)->findOneBy(['scope' => AVParameter::TYPE_SYNC_FUNCTION_DB, 'name' => $synFuncName]);
			if (!$paramObj) {
				$msg = "No function with name $synFuncName was found.";
				$this->loggerSrv->addWarning($msg);

				return;
			}
			$this->loggerSrv->addInfo("Query $synFuncName found. Starting parsing.");
			try {
				$sql = trim($paramObj->getValue());
				$stmt = $this->em->getConnection()->prepare($sql);
				$stmt->executeQuery();
				$this->loggerSrv->addInfo("Query $synFuncName run successfully.");
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError('Error running sync func', $thr);
				$this->loggerSrv->addInfo($thr->getMessage());
			}
		} while (0);
	}
}
