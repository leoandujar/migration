<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Connector\Qbo\Dto\InvoiceDto;
use App\Model\Entity\AmountModifier;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\QboItem;
use App\Model\Entity\Task;
use App\Model\Entity\TaskAmountModifier;
use App\Model\Entity\TaskCatCharge;
use App\Model\Entity\TaskCharge;
use App\Model\Entity\TaskFinance;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QboInvoicePrepareAction extends Action
{
	public const ACTION_DESCRIPTION = 'Prepare QBO Invoice Dto objects for given CustomerInvoices';
	public const ACTION_INPUTS = [
		'dbInvoices' => [
			'required' => true,
			'fromAction' => true,
			'type' => 'array',
			'description' => 'List of CustomerInvoice IDs.',
		],
		'prefix' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'string',
			'description' => 'The prefix to be removed from the path of the PDFs.',
		],
	];

	public const ACTION_OUTPUTS = [
		'dtoList' => [
			'description' => 'List of QBO Invoice Dto objects.',
			'type' => 'array',
		],
	];

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->actionName = 'QboInvoicePrepareAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$dbInvoices = $this->aux['dbInvoices'];
		$prefix = $this->aux['prefix'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			$dtoList = [];
			foreach ($dbInvoices as $id) {
				/** @var CustomerInvoice $ci */
				$ci = $this->em->getRepository(CustomerInvoice::class)->find($id);
				$this->loggerSrv->addInfo('[FLOW]: Starting prepare object for QBO Invoice Id: '.$ci->getId());
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
						$name = '';
						/** @var TaskAmountModifier $item */
						foreach ($amountModiList->getIterator() as $item) {
							/** @var AmountModifier $amountModifierObj */
							$amountModifierObj = $item->getAmountModifier();
							if ($amountModifierObj) {
								$name = $amountModifierObj->getName();
								$modDesc .= "$name, ";
							}
						}
						$qboItemName = 'Management Fee';
						if (str_contains($name, 'rush')) {
							$qboItemName = '40710 - Rush Fee';
						}
						if ($taskFinance->getTotalAmountModifier() < 0) {
							$qboItemName = '49200 - Discounts & Allowances - Other';
						}
						$qboItemId = $this->em->getRepository(QboItem::class)->findOneBy(['name' => $qboItemName])?->getId();
						$amount = round($taskFinance->getTotalAgreed() - $taskFinance->getTotalAgreed() / $taskFinance->getTotalAmountModifier(), 2);
						if (str_contains($targetLanguage, 'English')) {
							$description = "$modDesc$sourceLanguage";
						} else {
							$description = "$modDesc$targetLanguage";
						}
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
						$qboItemName = 'Management Fee';
						if (str_contains($name, 'rush')) {
							$qboItemName = '40710 - Rush Fee';
						}
						if ($taskFinance->getTotalAmountModifier() < 0) {
							$qboItemName = '49200 - Discounts & Allowances - Other';
						}
						$qboItemId = $this->em->getRepository(QboItem::class)->findOneBy(['name' => $qboItemName])?->getId();
						$amount = round($taskFinance->getTotalAgreed() - $taskFinance->getTotalAgreed() / $taskFinance->getTotalAmountModifier(), 2);
						$lastLineObj = $this->createLineDto($amount, $description, $qboItemId, false);
						$lines[] = $lastLineObj;
					}
					$total += round($taskFinance->getTotalAgreed(), 2);
				}
				if (count($lines)) {
					$invoiceDto = $this->createQboDto($lines, $ci->getTasks()->first(), $ci, $total);
					$path = $ci->getPdfPath();
					if (!empty($prefix)) {
						$path = str_replace($prefix, '', $path);
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
				$msg = '[FLOW]: There is not invoices dto in the list. Unable to continue';
				$this->sendErrorMessage(
					$msg,
					[
						'status' => 'Failure',
						'errorType' => 'No Invoices',
						'customerId' => $ci->getId() ?? null,
						'message' => '[FLOW]: Unable to continue due empty QBO Dto objects.',
					],
					null,
					null
				);
				throw new BadRequestHttpException($msg);
			}

			$this->outputs = [
				'dtoList' => $dtoList,
			];

			$this->setOutputs();

			$this->outputs = [];

			$this->sendSuccessMessage();

			return self::ACTION_STATUS_OK;
		} catch (\Throwable $thr) {
			$this->sendErrorMessage(null, null, $thr->getMessage(), null);

			return self::ACTION_STATUS_ERROR;
		}
	}

	private function addLineLog(Task $task, $entityId, $isCharge = false)
	{
		$this->sendErrorMessage(
			'Unable to create line for given data.',
			[
				'taskId' => $task->getCustomerInvoice()?->getId(),
				'taskNumberId' => $task->getCustomerInvoice()?->getFinalNumber(),
				'message' => 'Unable to create line for given data.',
				'data' => [
					'task' => $task->getId(),
					'target_entity' => $entityId,
					'is_charge' => $isCharge,
					'is_cat_charge' => !$isCharge,
				],
			],
			null,
			null
		);
	}

	private function addDtoLog(CustomerInvoice $ci)
	{
		$this->monitorLogSrv->appendError([
			'customerId' => $ci->getId(),
			'finalNumber' => $ci->getFinalNumber(),
			'message' => 'Unable to create invoice dto due lack of lines. No charges or catCharges found.',
		]);
	}

	private function processIterations(TaskFinance $taskFinance, $chargeCatCharge, &$sumAmount, string $description)
	{
		$amount = round($chargeCatCharge->getTotalValue(), 2);
		$minApplied = !$chargeCatCharge->getIgnoreMinimalCharge();
		$minValue = $chargeCatCharge->getMinimalCharge();
		if ($minApplied && ($amount < $minValue)) {
			$amount = round($minValue, 2);
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
					'value' => $qboItemId,
				],
				'Qty' => 1,
				'UnitPrice' => round($amount, 2),
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
