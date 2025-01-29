<?php

namespace App\MessageHandler;

use App\Message\WorkflowCleanFilesMessage;
use App\Model\Entity\WFHistory;
use App\Service\FileSystem\CloudFileSystemService;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class WorkflowCleanFilesMessageHandler
{
	private CloudFileSystemService $fileBucketService;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * ExpiredCommand constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em,
	) {
		$this->fileBucketService = $fileBucketService;
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
	}

	/**
	 * @throws OptimisticLockException
	 * @throws ORMException
	 */
	public function __invoke(WorkflowCleanFilesMessage $message): void
	{
		$this->loggerSrv->addInfo('Clean expired start');
		$expires = $this->em->getRepository(WFHistory::class)->expires();
		$count = 0;
		foreach ($expires as $expire) {
			$checkStorage = $this->fileBucketService->checkStorage($expire->getProvider());
			if ($checkStorage) {
				$this->fileBucketService->changeStorage($expire->getProvider());
				$this->fileBucketService->deleteFile($expire->getCloudName());
				$expire->setRemoved(true);
				++$count;
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($expire);
			} else {
				$this->loggerSrv->addWarning(sprintf('The disk: %s was not found on clean history command: history id: %s', $expire->getProvider(), $expire->getId()));
			}
		}
		$this->em->flush();

		$this->loggerSrv->addInfo(sprintf('Clean expired finished with %d files deleted', $count));

	}
}
