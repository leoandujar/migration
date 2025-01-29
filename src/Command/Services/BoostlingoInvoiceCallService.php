<?php

namespace App\Command\Services;

use App\Model\Entity\BlCall;
use App\Model\Entity\BlProviderInvoice;
use App\Model\Repository\BlCallRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LoggerService;
use App\Connector\Boostlingo\BoostlingoConnector;
use Symfony\Component\Console\Output\OutputInterface;

class BoostlingoInvoiceCallService
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private BlCallRepository $blCallRepo;
	private BoostlingoConnector $boostlingoConn;

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		BlCallRepository $blCallRepo,
		BoostlingoConnector $boostlingoConn
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->blCallRepo = $blCallRepo;
		$this->boostlingoConn = $boostlingoConn;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
	}

	public function processEntities(OutputInterface $output): void
	{
		try {
			$blInvoiceList = $this->em->getRepository(BlProviderInvoice::class)->findAll();
			$totalProcessedInvoices = $totalProcessedCalls = $totalSuccessInvoices = $totalSuccessCalls = $totalFailedInvoices = $totalFailedICalls = 0;

			if (!$this->isLogged($output)) {
				if (null === $this->boostlingoConn->signIn()) {
					$msg = 'User or password incorrect or unable to login into Boostlingo. ';
					$this->loggerSrv->addWarning($msg);
					$output->writeln($msg);

					return;
				}
			}
			/** @var BlProviderInvoice $blInvoice */
			foreach ($blInvoiceList as $blInvoice) {
				if (!$blInvoice->getBlProviderInvoiceId()) {
					++$totalFailedInvoices;
					continue;
				}
				++$totalProcessedInvoices;
				$output->writeln("Fetching data for Provider Invoice {$blInvoice->getBlProviderInvoiceId()}");
				$response = $this->boostlingoConn->retrieveInvoice($blInvoice->getBlProviderInvoiceId());
				if (!$response->isSuccessfull()) {
					$this->loggerSrv->addError("Error in retrieve invoice #{$blInvoice->getBlProviderInvoiceId()}=>{$response->getErrorMessage()}");
					++$totalFailedInvoices;
					continue;
				}

				$data = $response->getRaw();
				if (!$data) {
					$this->loggerSrv->addWarning("Remote Boostlingo Invoice #{$blInvoice->getBlProviderInvoiceId()} return no data.");
					++$totalFailedInvoices;
					continue;
				}

				++$totalSuccessInvoices;
				$calls = $data['calls'] ?? [];

				foreach ($calls as $call) {
					/** @var BlCall $blCallObj */
					$blCallObj = $this->blCallRepo->findOneBy(['blCallId' => $call['callLogId']]);
					if (!$blCallObj) {
						$this->loggerSrv->addNotice("BlCall for ID {$call['callLogId']} is not on DB. Skipping.");
						++$totalFailedICalls;
						continue;
					}
					$blCallObj->setBlProviderInvoiceId($blInvoice->getBlProviderInvoiceId());
					$this->em->persist($blCallObj);
					++$totalSuccessCalls;
					++$totalProcessedCalls;
				}
			}
			$this->em->flush();
			$output->writeln("TOTAL PROCESSED INVOICES $totalProcessedInvoices");
			$output->writeln("TOTAL SUCCESS INVOICES $totalSuccessInvoices");
			$output->writeln("TOTAL FAILED INVOICES $totalFailedInvoices");
			$output->writeln("TOTAL PROCESSED CALLS $totalProcessedCalls");
			$output->writeln("TOTAL SUCCESS CALLS$totalSuccessCalls");
			$output->writeln("TOTAL FAILED CALLS $totalFailedICalls");
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving the Bootslingo invoices.', $thr);
			$output->writeln($thr->getMessage());
		}
	}

	private function isLogged(OutputInterface $output): bool
	{
		try {
			$tokenExpiresAt = $this->boostlingoConn->getTokenExpiresAt();
			if (null !== $tokenExpiresAt) {
				$dateExpiresAt = new \DateTime($tokenExpiresAt);
				$token = $this->boostlingoConn->getToken();
				$now = new \DateTime('now');
				if (($dateExpiresAt > $now) && $token) {
					return true;
				}
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error checking boostlingo login.', $thr);
			$output->writeln($thr->getMessage());
		}

		return false;
	}
}
