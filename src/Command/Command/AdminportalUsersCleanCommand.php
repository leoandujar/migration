<?php

namespace App\Command\Command;

use App\Model\Repository\InternalUserRepository;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AdminportalUsersCleanCommand extends Command
{
    private LoggerService $loggerSrv;
    private EntityManagerInterface $em;
    private InternalUserRepository $internalUserRepo;

    public function __construct(
        LoggerService $loggerSrv,
        EntityManagerInterface $em,
        InternalUserRepository $internalUserRepo,
    ) {
        parent::__construct();
        $this->em = $em;
        $this->loggerSrv = $loggerSrv;
        $this->internalUserRepo = $internalUserRepo;
        $this->loggerSrv->setSubcontext(self::class);
        $this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
    }

    protected function configure(): void
    {
        $this
            ->setName('adminportal:users:clean')
            ->setDescription('Remove the old public users logged. This command allows you to delete the old users that are using public login in internalUser table. You can specify a date from delete til today, otherwise one week will take by default.')
            ->addOption(
                'since_date',
                'since',
                InputOption::VALUE_OPTIONAL,
                'Date from where users will be deleted based on last_login field. Can be human readable like -w'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tilDateParam = $input->getOption('since_date') ?? '1 week';
        $sinceDate = (new \DateTime('1970-01-01'))->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $tilDate = (new \DateTime('now'))->modify("-$tilDateParam")->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $output->writeln('Collecting users for delete.');
        $usersList = $this->internalUserRepo->findForPublicLogin($sinceDate, $tilDate);
        $output->writeln('Found=>'.count($usersList).' entities.');
        $totalDeleted = 0;
        foreach ($usersList as $entity) {
            $output->writeln("Deleting entity {$entity->getId()}");
            try {
                $this->em->remove($entity);
                ++$totalDeleted;
            } catch (\Throwable $thr) {
                $this->loggerSrv->addCritical('Error while deleting public login internal user.', $thr);
                $output->writeln("Unable to delete entity {$entity->getId()}. Skipping.");
            }
        }
        $this->em->flush();
        $output->writeln("TOTAL ENTITIES DELETED=>$$totalDeleted");

        return Command::SUCCESS;
    }
}
