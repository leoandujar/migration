<?php

namespace App\Workflow\Subscribers;

use App\Service\LoggerService;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WorkflowLogger implements EventSubscriberInterface
{
	private LoggerService $logger;

	public function __construct(LoggerService $logger)
	{
		$this->logger = $logger;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.request_workflow.leave' => 'onLeave',
		];
	}

	public function onLeave(Event $event)
	{
		$this->logger->alert($event);
	}
}
