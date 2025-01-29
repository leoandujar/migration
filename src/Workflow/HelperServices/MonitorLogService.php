<?php

namespace App\Workflow\HelperServices;

use App\Model\Entity\AvFlowMonitor;
use App\Model\Entity\AVWorkflowMonitor;
use Doctrine\ORM\EntityManagerInterface;

class MonitorLogService
{
	private mixed $monitor = null;
	private EntityManagerInterface $em;
	private array $details = [];
	private array $result = [];

	public function __construct(
		EntityManagerInterface $em,
	) {
		$this->em = $em;
	}

	public function getMonitor(): AVWorkflowMonitor|AvFlowMonitor|null
	{
		return $this->monitor;
	}

	public function setMonitor(AVWorkflowMonitor|AvFlowMonitor $monitor): void
	{
		$this->monitor = $monitor;
		$this->details = $monitor->getDetails();
	}

	public function appendParams(array $params): bool
	{
		if (!$this->monitor) {
			return false;
		}

		if ($this->monitor instanceof AVWorkflowMonitor) {
			if (!isset($this->details['params'])) {
				$this->details['params'] = [];
			}
			$this->details['params'][] = $params;
		} else {
			$this->details = $params;
		}

		$this->monitor->setDetails($this->details);

		$this->save();

		return true;
	}

	public function appendError(array $error): bool
	{
		if (!$this->monitor) {
			return false;
		}

		if ($this->monitor instanceof AVWorkflowMonitor) {
			$this->details = $this->monitor->getDetails();
			if (!isset($this->details['errors'])) {
				$this->details['errors'] = [];
			}
			$this->details['errors'][] = $error;
			$this->monitor->setDetails($this->details);
		} else {
			$this->result = $this->monitor->getResult();
            if (!isset($this->result['errors'])) {
                $this->result['errors'] = [];
            }
			$this->result['errors'][] = $error;
			$this->monitor->setResult($this->result);
		}

		$this->save();

		return true;
	}

	public function appendSuccess(array $success): bool
	{
		if (!$this->monitor) {
			return false;
		}

		if ($this->monitor instanceof AVWorkflowMonitor) {
			$this->details = $this->monitor->getDetails();
			if (!isset($this->details['successful'])) {
				$this->details['successful'] = [];
			}
			$this->details['successful'][] = $success;
			$this->monitor->setDetails($this->details);
		} else {
			$this->result = $this->monitor->getResult();
            if (!isset($this->result['successful'])) {
                $this->result['successful'] = [];
            }
			$this->result['successful'][] = $success;
			$this->monitor->setResult($this->result);
		}
		$this->save();

		return true;
	}

	private function save(): void
	{
		if (null !== $this->monitor) {
			$this->em->persist($this->monitor);
			$this->em->flush();
		}
	}
}
