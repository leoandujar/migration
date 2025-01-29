<?php

namespace App\Command\Services;

use App\Model\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoggerService;
use App\Model\Entity\BlCustomer;
use App\Connector\Boostlingo\BoostlingoConnector;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BoostlingoFetchService.
 */
class BoostlingoRetrieveClientService
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private CustomerRepository $customerRepo;
	private BoostlingoConnector $boostlingoConn;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		CustomerRepository $customerRepo,
		BoostlingoConnector $boostlingoConn
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->customerRepo = $customerRepo;
		$this->boostlingoConn = $boostlingoConn;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function processClients(OutputInterface $output): void
	{
		try {
			$blCustomerList = $this->em->getRepository(BlCustomer::class)->findAll();
			$totalProcessed = $totalSuccess = $totalFailed = 0;
			/** @var BlCustomer $blCustomer */
			foreach ($blCustomerList as $blCustomer) {
				if (!$blCustomer->getBlCustomerId()) {
					++$totalFailed;
					continue;
				}
				$output->writeln("Fetching data for Client {$blCustomer->getBlCustomerId()}");
				$response = $this->boostlingoConn->retrieveClient($blCustomer->getBlCustomerId());
				if (!$response->isSuccessfull()) {
					$this->loggerSrv->addError("Error in retrieve client #{$blCustomer->getBlCustomerId()}=>{$response->getErrorMessage()}");
					++$totalFailed;
					continue;
				}

				$data = $response->getRaw();
				if (!$data || !isset($data['uniqueId']) || empty($data['uniqueId'])) {
					$this->loggerSrv->addWarning("Remote Boostlingo Client #{$blCustomer->getBlCustomerId()} has empty uniqueId");
					++$totalFailed;
					continue;
				}

				$customerObj = $this->customerRepo->find($data['uniqueId']);
				if (!$customerObj) {
					$this->loggerSrv->addWarning("Unable to find on DB customer with uniqueId {$data['uniqueId']}");
					++$totalFailed;
					continue;
				}

				$blCustomer->setCustomer($customerObj);
				$this->em->persist($blCustomer);
				++$totalProcessed;
			}

			$this->em->flush();
			$output->writeln("TOTAL PROCESSED $totalProcessed");
			$output->writeln("TOTAL SUCCESS $totalSuccess");
			$output->writeln("TOTAL FAILED $totalFailed");
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving the Bootslingo clients .', $thr);
			$output->writeln($thr->getMessage());
		}
	}
}
