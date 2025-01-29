<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Flow\Utils\FlowUtils;
use App\Model\Entity\CustomerInvoice;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EntityInvoicesCollectAction extends Action
{
	public const ACTION_DESCRIPTION = 'Get Invoices from Database by filters';
	public const ACTION_INPUTS = [
		'filtersInvoices' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Filters to search invoices in the database.',
		],
	];

	public const ACTION_OUTPUTS = [
		'dbInvoices' => [
			'description' => 'List of invoices found in the database.',
			'type' => 'array',
		],
	];
	private FlowUtils $flowUtils;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
		FlowUtils $flowUtils,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->flowUtils = $flowUtils;
		$this->actionName = 'EntityInvoicesCollectAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filters = $this->aux['filtersInvoices'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if (empty($filters)) {
				throw new BadRequestHttpException('[FLOW]: No filters was found. Unable to continue.');
			}

			if (!$this->flowUtils->prepareCollectInvoicesQbo($filters)) {
				throw new BadRequestHttpException('[FLOW]: Filters are no valid. Unable to continue.');
			}

			$dbInvoiceListObj = $this->em->getRepository(CustomerInvoice::class)->getSearchInvoicesIds($filters);

			if (!$dbInvoiceListObj) {
				throw new BadRequestHttpException("[FLOW]: There is not invoices with provided filters. Unable to continue. MonitorId: $this->monitorId");
			}

			$dbInvoiceList = [array_shift($dbInvoiceListObj)];

			$this->monitorLogSrv->getMonitor()->setAuxiliaryData($dbInvoiceListObj);
			$this->em->persist($this->monitorLogSrv->getMonitor());
			$this->em->flush();

			$this->outputs = [
				'dbInvoices' => $dbInvoiceList,
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
}
