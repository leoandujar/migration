<?php

namespace App\MessageHandler;

use App\Message\AdminportalUsersCleanMessage;
use App\Model\Entity\InternalUser;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AdminportalUsersCleanMessageHandler
{
	private EntityManagerInterface $em;
	private LoggerService $loggerSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
	}

	/**
	 * @throws \DateMalformedStringException
	 */
	public function __invoke(AdminportalUsersCleanMessage $message): void
	{
		$tilDateParam = $message->getSinceDate();
		$sinceDate = (new \DateTime('1970-01-01'))->setTime(0, 0, 0)->format('Y-m-d H:i:s');
		$tilDate = (new \DateTime('now'))->modify("-$tilDateParam")->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		$this->loggerSrv->addInfo('Fetching users for delete.');
		$usersList = $this->em->getRepository(InternalUser::class)->findForPublicLogin($sinceDate, $tilDate);
		$totalDeleted = 0;
		foreach ($usersList as $entity) {
			try {
				$this->em->remove($entity);
				++$totalDeleted;
				$this->loggerSrv->addInfo("Deleted entity {$entity->getId()}");
			} catch (\Throwable $thr) {
				$this->loggerSrv->addCritical('Error while deleting public login internal user.', $thr);
			}
		}
		$this->em->flush();
		$this->loggerSrv->addInfo("Total deleted entities: $totalDeleted");
	}
}
