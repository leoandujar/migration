<?php

namespace App\MessageHandler;

use App\Connector\Boostlingo\BoostlingoConnector;
use App\Message\ConnectorsBoostlingoSyncMessage;
use App\Model\Entity\BlCustomer;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConnectorsBoostlingoSyncMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private BoostlingoConnector $boostlingoConn;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		BoostlingoConnector $boostlingoConn,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->boostlingoConn = $boostlingoConn;
	}

	public function __invoke(ConnectorsBoostlingoSyncMessage $message): void
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
				$this->loggerSrv->addInfo("Fetching data for Client {$blCustomer->getBlCustomerId()}");
				$response = $this->boostlingoConn->retrieveClient(clientId: $blCustomer->getBlCustomerId());
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
				$customerObj = $this->em->getRepository(BlCustomer::class)->find(id:$data['uniqueId']);
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
			$this->loggerSrv->addInfo("TOTAL PROCESSED $totalProcessed");
			$this->loggerSrv->addInfo("TOTAL SUCCESS $totalSuccess");
			$this->loggerSrv->addInfo("TOTAL FAILED $totalFailed");
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving the Bootslingo clients .', $thr);
		}
	}
}
