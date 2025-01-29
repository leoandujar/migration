<?php

namespace App\Flow\Actions;

use App\Model\Entity\AvFlowMonitor;
use App\Model\Entity\AvFlowAction;
use App\Service\LoggerService;
use App\Workflow\HelperServices\MonitorLogService;
use Doctrine\ORM\EntityManagerInterface;

abstract class Action
{
	public const ACTION_STATUS_OK = 'OK';
	public const ACTION_STATUS_ERROR = 'ERROR';
	public const PROCESS_STATUS_SUCCESS = 'success';
	public const PROCESS_STATUS_FAILURE = 'failure';
	public const PROCESS_STATUS_TERMINATE = 'terminate';

	protected EntityManagerInterface $em;
	protected LoggerService $loggerSrv;
	protected MonitorLogService $monitorLogSrv;
	protected array $inputs;
	protected array $outputs;
	protected array $aux = [];
	protected array $params = [];
	protected ?AvFlowMonitor $monitorObj;
	protected ?string $type;
	protected string $monitorId;
	protected string $actionName;
	protected ?string $actionId;
	protected mixed $specificInput;
	protected array $actionInputs;
	protected string $slug;

	public function __construct(
		EntityManagerInterface $em,
		LoggerService $loggerSrv,
		MonitorLogService $monitorLogSrv,
	) {
		$this->em = $em;
		$this->loggerSrv = $loggerSrv;
		$this->monitorLogSrv = $monitorLogSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT);
	}

	abstract public function run(): string;

	public function getType(): string
	{
		return $this->type;
	}

	public function setSlug(string $slug): void
	{
		$this->slug = $slug;
	}

	public function setSpecificInput(mixed &$input): void
	{
		$this->specificInput = &$input;
	}

	public function getSpecificInput(): mixed
	{
		return $this->specificInput;
	}

	public function getActionId(): string
	{
		return $this->actionId;
	}

	public function setActionId(string $actionId): void
	{
		$this->actionId = $actionId;
	}

	public function getActions(): array
	{
		return $this->em->getRepository(AvFlowAction::class)->findBy(['parent' => $this->actionId]);
	}

	public function setMonitorId(string $monitorId): void
	{
		$this->monitorId = $monitorId;
	}

	protected function retrieveMonitor(): void
	{
	}

	protected function updateMonitor(): void
	{
	}

	/**
	 * @throws \Exception
	 */
	protected function setMonitorObject(): void
	{
		$flowMonitorRepo = $this->em->getRepository(AvFlowMonitor::class);
		$monitorObj = $flowMonitorRepo->find($this->monitorId);
		if ($monitorObj) {
			$this->monitorLogSrv->setMonitor($monitorObj);
		} else {
			throw new \Exception('Monitor not found');
		}
	}

	protected function getInputs(): void
	{
		$this->monitorObj = $this->em->getRepository(AvFlowMonitor::class)->find($this->monitorId);
		$this->overrideInputs();
		$this->params = $this->monitorObj->getDetails();
		foreach ($this->inputs as $name => $input) {
			$input = $this->actionInputs[$name] ?? null;

			if ($input && is_string($input) && preg_match("/^[^.]+\.outputs\.[^.]+$/", $input)) {
				$nameParts = explode('.', $input);
				$this->aux[$name] = $this->params[$nameParts[0]][$nameParts[1]][$nameParts[2]] ?? null;
			} else {
				$this->aux[$name] = $this->actionInputs[$name]['value'] ?? null;
			}
		}
	}

	protected function overrideInputs(): void
	{
		$details = $this->monitorObj->getDetails();
		if (key_exists($this->slug, $details)) {
			foreach ($details[$this->slug] as $key => $value) {
				$this->actionInputs[$key]['value'] = $value;
			}
		}
		unset($details);
	}

	public function setActionInputs(array $inputs): void
	{
		$this->actionInputs = $inputs;
	}

	protected function getOneInput(string $inputName): mixed
	{
		return $this->params[$inputName];
	}

	protected function setOutputs(): void
	{
		$this->aux = [];
		foreach ($this->outputs as $key => $value) {
			$this->params[$this->slug]['outputs'][$key] = $value;
		}

		$this->monitorObj->setDetails($this->params);
		$this->em->persist($this->monitorObj);
		$this->em->flush();
	}

	protected function sendSuccessMessage(): void
	{
		$this->loggerSrv->addInfo('[FLOW]: The action '.$this->actionName."' was executed successfully!.");
	}

	protected function sendErrorMessage(?string $message, ?array $data, ?string $exceptionMsg, ?string $critical): void
	{
		$defaultMonitorMessage = '[FLOW]: Workflow finished with error on '.$this->actionName.', got exception.';

		$this->monitorLogSrv->appendError($data ?? ['message' => $message ?? $defaultMonitorMessage]);

		$defaultLoggerMessage = '[FLOW]: Workflow finished with error on '.$this->actionName.'.';

		$this->loggerSrv->addError($message ?? $defaultLoggerMessage, $exceptionMsg ?? 'Undefined Exception');

		if ($critical) {
			$this->loggerSrv->addCritical($critical);
		}
	}

	protected function sendSuccess(?array $data): void
	{
		$this->monitorLogSrv->appendSuccess($data);
	}

	protected function sendStartMessage(): void
	{
		$this->loggerSrv->addInfo('[FLOW]: Starting '.$this->actionName.' action.');
	}
}
