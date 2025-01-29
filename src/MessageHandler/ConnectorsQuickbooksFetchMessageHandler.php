<?php

namespace App\MessageHandler;

use App\Command\Services\QuickBooksEntitiesUpdateService;
use App\Connector\Qbo\QboConnector;
use App\Linker\Services\QboService;
use App\Linker\Services\RedisClients;
use App\Message\ConnectorsQuickbooksFetchMessage;
use App\Service\LoggerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ConnectorsQuickbooksFetchMessageHandler
{
	private const ENTITIES = ['Account', 'Item', 'Invoice', 'InvoiceItems', 'Payment', 'Bill', 'Vendor', 'BillPayment', 'Customer'];

	private QboConnector $qboConnector;
	private QboService $qboService;
	private LoggerService $loggerSrv;
	private RedisClients $redisClients;

	/**
	 * QuickBooksEntitiesUpdateService constructor.
	 */
	public function __construct(
		QboConnector $qboConnector,
		QboService $qboService,
		LoggerService $loggerSrv,
		RedisClients $redisClients,
	) {
		$this->qboConnector = $qboConnector;
		$this->qboService = $qboService;
		$this->loggerSrv = $loggerSrv;
		$this->redisClients = $redisClients;
	}

	public function __invoke(ConnectorsQuickbooksFetchMessage $message): void
    {
		$totalToProcess = $this->collect(message: $message);
		[$success, $failed] = $this->process();
		$this->loggerSrv->addInfo(sprintf('Processed Success %d Failed %d from Total: ', $success, $failed, $totalToProcess));
		//		return (0 === $failed) ? Command::SUCCESS : Command::FAILURE;
	}

	/**
	 * @param string $operation
	 */
	private function processEntities($id, $entity, $operation = QboService::OPERATION_CREATE): bool
	{
		$result = match ($entity) {
			QboService::ENTITY_NAME_CUSTOMER => null !== $this->qboService->processCustomer($id, $operation),
			QboService::ENTITY_NAME_ACCOUNT => null !== $this->qboService->processAccount($id, $operation),
			QboService::ENTITY_NAME_BILL => null !== $this->qboService->processBill($id, $operation),
			QboService::ENTITY_NAME_INVOICE => null !== $this->qboService->processInvoice($id, $operation),
			QboService::ENTITY_NAME_VENDOR => null !== $this->qboService->processQboProvider($id, $operation),
			QboService::ENTITY_NAME_ITEM => null !== $this->qboService->processQboItem($id, $operation),
			QboService::ENTITY_NAME_CUSTOMER_PAYMENT => null !== $this->qboService->processQboCustomerPayment($id, $operation),
			QboService::ENTITY_NAME_BILL_PAYMENT => null !== $this->qboService->processQboProviderPayment($id, $operation),
			default => false,
		};

		return $result ? 1 : 0;
	}

	/**
	 * @param null $id
	 */
	private function collectEntities($entity, $id = null): array
	{
		$itemNumber = 0;
		$pageNumber = 0;
		$key = sprintf('%s-%s', RedisClients::SESSION_KEY_QUEUE_ALIAS_PAGE, $entity);
		if ($this->redisClients->redisMainDB->exists($key)) {
			$itemNumber = intval($this->redisClients->redisMainDB->get($key));
		}
		$entitiesCount = 0;
		$this->loggerSrv->addInfo(sprintf('Collecting entity %s', $entity));
		try {
			do {
				$this->loggerSrv->addInfo("<header>Fetching page $pageNumber from position $itemNumber</header>");
				if (empty($id)) {
					$entities = $this->qboConnector->findAll($entity, $itemNumber, RedisClients::DEFAULT_PAGE_SIZE);
				} else {
					$entities = [$this->qboConnector->findById($entity, $id)];
				}
				if (!is_array($entities)) {
					break;
				}
				$entitiesCount += count($entities);
				foreach ($entities as $item) {
					if (null === $item) {
						continue;
					}
					$this->loggerSrv->addInfo(sprintf('<entname>Adding %s ID: %s to the queue</entname>', $entity, $item->Id));
					$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_QUEUE_ALIAS, json_encode(['entity' => $entity, 'id' => $item->Id]));
				}
				$itemNumber += RedisClients::DEFAULT_PAGE_SIZE + 1;
				++$pageNumber;
				$this->redisClients->redisMainDB->set($key, $itemNumber);
			} while (RedisClients::DEFAULT_PAGE_SIZE == count($entities));
		} catch (\Throwable $exception) {
			$this->loggerSrv->addError($exception);
		}
		$this->loggerSrv->addInfo(sprintf('Collect for entity %s finished, Total entities: %d', $entity, $entitiesCount));

		return [$entitiesCount, $itemNumber];
	}

	public function process(): array
	{
		$this->loggerSrv->addInfo('<header>Process collected data entities.</header>');
		$success = 0;
		$failed = 0;
		while (true) {
			$payload = $this->redisClients->redisMainDB->lpop(RedisClients::SESSION_KEY_QUEUE_ALIAS);
			if (null === $payload) {
				$this->loggerSrv->addWarning('Qbo queue is empty.');
				break;
			}
			$payload = json_decode($payload);
			$ok = false;
			try {
				$ok = $this->processEntities($payload->id, $payload->entity);
			} catch (\Throwable $thr) {
				$msg = sprintf('unable to process entity %s with id: %s', $payload->entity, $payload->id);
				$this->loggerSrv->addError($msg, $thr);
			}
			$this->loggerSrv->addInfo(sprintf('<entname>Processing %s ID: %s %s</entname>', $payload->entity, $payload->id, $ok ? 'Stored' : 'Not Stored'));
			if ($ok) {
				$success += $ok;
			} else {
				++$failed;
			}
		}

		return [$success, $failed];
	}

	public function collect(ConnectorsQuickbooksFetchMessage $message): mixed
	{
		$this->loggerSrv->addInfo('<header>Collecting entities to process.</header>');
		$entities = self::ENTITIES;
		$entityID = $message->getId();
		$entity = $message->getEntity();
		if ('' !== $entityID && '' === $entity) {
			$this->loggerSrv->addError('To provide entity id the entity name should be provided.');

			return 1;
		}
		if (in_array($entity, $entities)) {
			$entities = [$message->getEntity()];
		}
		$totalToProcess = 0;
		foreach ($entities as $entity) {
			list($totalEntities, $totalPages) = $this->collectEntities($entity, $entityID);
			$this->loggerSrv->addInfo(sprintf('<entname>%s Total: %s Pages: %s</entname>', $entity, $totalEntities, $totalPages));
			$totalToProcess += $totalEntities;
		}

		return $totalToProcess;
	}
}
