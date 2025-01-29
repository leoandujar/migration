<?php

namespace App\Workflow\Subscribers\XtrfQbo;

use App\Connector\Qbo\Dto\InvoiceDto;
use App\Model\Entity\AmountModifier;
use App\Model\Entity\AVWorkflowMonitor;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\QboItem;
use App\Model\Entity\Task;
use App\Model\Entity\TaskAmountModifier;
use App\Model\Entity\TaskCatCharge;
use App\Model\Entity\TaskCharge;
use App\Model\Entity\TaskFinance;
use App\Model\Entity\WFHistory;
use App\Model\Repository\CustomerInvoiceRepository;
use App\Model\Repository\WorkflowMonitorRepository;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;

class PrepareObjects implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private MonitorLogService $monitorLogSrv;
	private CustomerInvoiceRepository $ciRepo;
	private WorkflowMonitorRepository $wfMonitorRepo;

	public function __construct(
		Registry $registry,
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		MonitorLogService $monitorLogSrv,
		CustomerInvoiceRepository $ciRepo,
		WorkflowMonitorRepository $wfMonitorRepo
	) {
		$this->em = $em;
		$this->ciRepo = $ciRepo;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->wfMonitorRepo = $wfMonitorRepo;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_QBO);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.wf_xtrf_qbo.completed.collect' => 'prepareObjects',
		];
	}

	public function prepareObjects(Event $event)
	{
		$this->loggerSrv->addInfo('Starting parepare object for QBO Invoicing');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		if ($context['monitor_id']) {
			/** @var AVWorkflowMonitor $monitorObj */
			$monitorObj = $this->wfMonitorRepo->find($context['monitor_id']);
			if ($monitorObj) {
				$this->monitorLogSrv->setMonitor($monitorObj);
			}
		}
		$dbInvoices = $context['dbInvoices'];
		unset($context['dbInvoices']);
		try {
			$dtoList = [];

			foreach ($dbInvoices as $id) {
				/** @var CustomerInvoice $ci */
				$ci = $this->ciRepo->find($id);
				$this->loggerSrv->addInfo('Starting prepare object for QBO Invoice Id: '.$ci->getId());
				$lines = [];
				$total = 0;
				/** @var Task $task */
				foreach ($ci->getTasks()->getIterator() as $task) {
					$taskFinance = $task->getProjectPartFinance();
					$catCharges = $taskFinance->getTaskCatCharges()->getIterator();
					$charges = $taskFinance->getTaskCharges()->getIterator();
					$targetLanguage = $task->getTargetLanguage()?->getName();
					$sourceLanguage = $task->getSourceLanguage()?->getName();
					$sumAmountCharges = 0;
					$sumAmountCatCharges = 0;
					$amountModiList = $taskFinance->getAmountModifiersList();

					/** @var TaskCharge $charge */
					foreach ($charges as $charge) {
						$activityType = $charge->getActivityType()?->getName();
						$description = "$activityType:$targetLanguage";
						$iterObj = $this->processIterations($taskFinance, $charge, $sumAmountCharges, $description);
						if (null === $iterObj) {
							$this->addLineLog($task, $charge->getId(), true);
							continue 3;
						}
						$lines[] = $iterObj;
					}

					/** @var TaskCatCharge $catCharge */
					foreach ($catCharges as $catCharge) {
						$activityType = $catCharge->getActivityType()?->getName();
						$description = "$activityType:$targetLanguage";
						$iterObj = $this->processIterations($taskFinance, $catCharge, $sumAmountCatCharges, $description);
						if (null === $iterObj) {
							$this->addLineLog($task, $catCharge->getId());
							continue 3;
						}
						$lines[] = $iterObj;
					}

					if ($amountModiList->count() > 0) {
						$modDesc = '';
						/** @var TaskAmountModifier $item */
						foreach ($amountModiList->getIterator() as $item) {
							/** @var AmountModifier $amountModifierObj */
							$amountModifierObj = $item->getAmountModifier();
							if ($amountModifierObj) {
								$name = $amountModifierObj->getName();
								$modDesc .= "$name, ";
							}
						}
						$amount = $taskFinance->getTotalAgreed() - $taskFinance->getTotalAgreed() / $taskFinance->getTotalAmountModifier();
						if (str_contains($targetLanguage, 'English')) {
							$description = "$modDesc$sourceLanguage";
						} else {
							$description = "$modDesc$targetLanguage";
						}
						$qboItemId = $this->em->getRepository(QboItem::class)->findOneBy(['name' => 'Management Fee'])?->getId();
						$lastLineObj = $this->createLineDto($amount, $description, $qboItemId, false);
						$lines[] = $lastLineObj;
					} elseif ($taskFinance->getManualAmountModifierName()) {
						$name = $taskFinance->getManualAmountModifierName();
						$modDesc = "$name, ";
						if (str_contains($targetLanguage, 'English')) {
							$description = "$modDesc$sourceLanguage";
						} else {
							$description = "$modDesc$targetLanguage";
						}
						$amount = $taskFinance->getTotalAgreed() - $taskFinance->getTotalAgreed() / $taskFinance->getTotalAmountModifier();
						$qboItemId = $this->em->getRepository(QboItem::class)->findOneBy(['name' => 'Management Fee'])?->getId();
						$lastLineObj = $this->createLineDto($amount, $description, $qboItemId, false);
						$lines[] = $lastLineObj;
					}
					$total += round($taskFinance->getTotalAgreed(), 2);
				}
				if (count($lines)) {
					$invoiceDto = $this->createQboDto($lines, $ci->getTasks()->first(), $ci, $total);
					$path = $ci->getPdfPath();
					if (!empty($context['prefix'])) {
						$path = str_replace($context['prefix'], '', $path);
					}
					$dtoList[] = [
						'dto' => $invoiceDto,
						'path' => $path,
					];
				} else {
					$this->addDtoLog($ci);
				}
			}
			if (!count($dtoList)) {
				$msg = 'There is not invoices dto in the list. Unable to continue';
				$this->loggerSrv->addError($msg);
				$this->monitorLogSrv->appendError([
					'message' => 'Unable to continue due empty QBO Dto objects.',
				]);
				throw new BadRequestHttpException($msg);
			}

			$context['dtoList'] = $dtoList;
			$wf = $this->registry->get($history, 'wf_xtrf_qbo');

			if ($wf->can($history, 'prepare')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepare');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error in Prepare objects step for XTRF-QBO workflow.', $thr);
			throw $thr;
		}
	}

	private function addLineLog(Task $task, $entityId, $isCharge = false)
	{
		$this->monitorLogSrv->appendError([
			'id' => $task->getCustomerInvoice()?->getId(),
			'number' => $task->getCustomerInvoice()?->getFinalNumber(),
			'message' => 'Unable to create line for given data.',
			'data' => [
				'task' => $task->getId(),
				'target_entity' => $entityId,
				'is_charge' => $isCharge,
				'is_cat_charge' => !$isCharge,
			],
		]);
	}

	private function addDtoLog(CustomerInvoice $ci)
	{
		$this->monitorLogSrv->appendError([
			'id' => $ci->getId(),
			'number' => $ci->getFinalNumber(),
			'message' => 'Unable to create invoice dto due lack of lines. No charges or catCharges found.',
		]);
	}

	private function processIterations(TaskFinance $taskFinance, $chargeCatCharge, &$sumAmount, string $description)
	{
		$amount = $chargeCatCharge->getTotalValue();
		$minApplied = !$chargeCatCharge->getIgnoreMinimalCharge();
		$minValue = $chargeCatCharge->getMinimalCharge();
		if ($minApplied && ($amount < $minValue)) {
			$amount = $minValue;
		}
		$sumAmount += $amount;
		$lineObj = $this->createLineDto($amount, $description, $chargeCatCharge->getActivityType()?->getQboItem()?->getId());
		if (!$lineObj) {
			return null;
		}

		return $lineObj;
	}

	private function createLineDto($amount, string $description, $qboItemId = null, bool $isLine = true): ?array
	{
		if (empty($qboItemId) && $isLine) {
			return null;
		}

		return [
			'DetailType' => 'SalesItemLineDetail',
			'Amount' => $amount,
			'SalesItemLineDetail' => [
				'ItemRef' => [
					'value' => $qboItemId, // hook if no getQboItemId, add to result but SKIP creation
				],
				'Qty' => 1,
				'UnitPrice' => $amount,
			],
			'Description' => $description,
		];
	}

	private function createQboDto(array $lines, Task $firstTask, CustomerInvoice $ci, float $totalAmount): InvoiceDto
	{
		$qboDto = new InvoiceDto(
			$ci->getCustomer()->getQboId(),
			$lines,
			$totalAmount,
			$ci->getFinalNumber(),
			$ci->getRequiredPaymentDate()->format('Y-m-d'),
			$ci->getFinalDate()->format('Y-m-d'),
			[
				[
					'DefinitionId' => '3',
					'Type' => 'StringType',
					'StringValue' => $firstTask->getProject()->getBillingContact() ?? 'Contact not set',
				],
				[
					'DefinitionId' => '2',
					'Type' => 'StringType',
					'StringValue' => $firstTask->getProject()->getCostCenter() ?? 'CostCenter not set',
				],
				[
					'DefinitionId' => '1',
					'Type' => 'StringType',
					'StringValue' => $firstTask->getProject()->getNuid() ?? 'Nuid not set',
				],
			]
		);

		return $qboDto;
	}
}
