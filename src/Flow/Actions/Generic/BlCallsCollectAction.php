<?php

namespace App\Flow\Actions\Generic;

use App\Flow\Actions\Action;
use App\Flow\Utils\FlowUtils;
use App\Model\Entity\BlCall;
use App\Model\Entity\BlCustomer;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BlCallsCollectAction extends Action
{
	public const ACTION_DESCRIPTION = 'Get all calls from BL customers and prepare the data to send to XTRF.';
	public const ACTION_INPUTS = [
		'filtersBlCalls' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Filters to get the calls from BL customers',
		],
		'templateBlCalls' => [
			'required' => true,
			'fromAction' => false,
			'type' => 'array',
			'description' => 'Template to create the XTRF project',
		],
	];

	public const ACTION_OUTPUTS = [
		'template' => [
			'description' => 'For each Boostlingo client creates a template for XTRF project.',
			'type' => 'array',
		],
	];
	protected const ACTION_NAME = 'BlCallsCollectAction';
	private FlowUtils $flowUtils;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
		FlowUtils $flowUtils,
	) {
		parent::__construct($em, $loggerSrv, $monitorLogSrv);
		$this->flowUtils = $flowUtils;
		$this->actionName = 'BlCallsCollectAction';
		$this->inputs = self::ACTION_INPUTS;
	}

	public function run(): string
	{
		$this->getInputs();
		$filters = $this->aux['filtersBlCalls'];
		$template = $this->aux['templateBlCalls'];

		$this->sendStartMessage();

		try {
			$this->setMonitorObject();

			if (empty($filters)) {
				throw new BadRequestHttpException('[FLOW]: No filters was found. Unable to continue.');
			}
			if (!$this->flowUtils->prepareCollectBlCalls($filters)) {
				throw new BadRequestHttpException('[FLOW]: Filters are no valid. Unable to continue.');
			}
			$blCustomers = $filters['customer_id'];
			if (!$blCustomers) {
				throw new BadRequestHttpException("[FLOW]: There is no customers with provided filters. Unable to continue. Monitor: $this->monitorId");
			}

			$xtrfRequests = [];

			foreach ($blCustomers as $blCustomerId) {
				$blCustomer = $this->em->getRepository(BlCustomer::class)->find($blCustomerId);
				if ($blCustomer) {
					$calls = $this->em->getRepository(BlCall::class)->getCallsByCustomer($blCustomerId, $filters);
					$targetLangIds = array_values(array_unique(array_column($calls, 'languageId')));
					$macroParams = [
						'languages' => [],
					];
					$projectRequest = [
						'customerId' => $blCustomer->getCustomer()->getId(),
						'serviceId' => $template['service'],
						'specializationId' => $template['specialization'],
						'sourceLanguageId' => $template['source_language'],
						'targetLanguagesIds' => $targetLangIds,
					];
					foreach ($calls as $call) {
						if (!empty($call['languageId'])) {
							$call['date'] = $call['date']->format('Y-m-d H:i');
							$macroParams['languages'][$call['languageId']]['payable'][] = [
								'minutes' => floatval($call['duration']),
								'rate' => $call['duration'] > 0 ? $call['blAmount'] / $call['duration'] : 0,

								'description' => json_encode($call),
							];
							$macroParams['languages'][$call['languageId']]['receivable'][] = [
								'minutes' => floatval($call['duration']),
								'rate' => $call['duration'] > 0 ? $call['amount'] / $call['duration'] : 0,
								'description' => json_encode($call),
							];
						}
					}
					$xtrfRequests[] = [
						'projectRequests' => $projectRequest,
						'macroParams' => $macroParams,
					];
				}
			}

			$this->outputs = [
				'template' => $xtrfRequests,
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
