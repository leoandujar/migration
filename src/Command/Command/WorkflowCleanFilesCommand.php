<?php

namespace App\Command\Command;

use App\Service\LoggerService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileSystem\CloudFileSystemService;
use App\Model\Repository\WFHistoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WorkflowCleanFilesCommand extends Command
{
	private CloudFileSystemService $fileBucketService;
	private WFHistoryRepository $historyRepository;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;

	/**
	 * ExpiredCommand constructor.
	 */
	public function __construct(
		WFHistoryRepository $historyRepository,
		LoggerService $loggerSrv,
		CloudFileSystemService $fileBucketService,
		EntityManagerInterface $em,
		string $name = null
	) {
		parent::__construct($name);
		$this->fileBucketService = $fileBucketService;
		$this->historyRepository = $historyRepository;
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->loggerSrv->setSubcontext(self::class);
	}

	protected function configure(): void
	{
		$this
			->setName('workflow:clean:files')
			->setDescription('Workflow: Remove the expired files from the cloud.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('Clean expired start');
		$expires = $this->historyRepository->expires();
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

		$output->writeln(sprintf('Clean expired finished with %d files deleted', $count));

		return Command::SUCCESS;
	}
}
