<?php

namespace App\Workflow\Subscribers\EmailParsing;

use App\Workflow\HelperServices\EmailParsingService;
use App\Service\LoggerService;
use App\Model\Entity\WFHistory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PrepareData implements EventSubscriberInterface
{
	private Registry $registry;
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
	private EmailParsingService $emailParsingSrv;

	public function __construct(
		LoggerService $loggerSrv,
		EmailParsingService $emailParsingSrv,
		EntityManagerInterface $em,
		Registry $registry
	) {
		$this->em = $em;
		$this->registry = $registry;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_EMAIL_PARSING);
		$this->emailParsingSrv = $emailParsingSrv;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.email_parsing.completed.initialized' => 'prepareData',
		];
	}

	public function prepareData(Event $event)
	{
		$this->loggerSrv->addInfo('Preparing data from mapping fields to project data.');
		/** @var WFHistory $history */
		$history = $event->getSubject();
		$context = $history->getContext();
		$data = $context['data'];
		$mappings = $context['mapping'];
		$params = $context['params'];
		unset($context['mapping'], $context['data']);

		if (empty($mappings)) {
			$msg = 'Mappings is empty. Unable to continue.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}
		$globalPrefix = $mappings['default_prefix'] ?? null;

		if (!$globalPrefix) {
			$msg = 'Default mapping delimiter can not be empty. Unable to continue.';
			$this->loggerSrv->addError($msg);
			throw new BadRequestHttpException($msg);
		}

		try {
			$files = [];
			$wf = $this->registry->get($history, 'email_parsing');
			$this->emailParsingSrv->initMappings($mappings);
			$this->emailParsingSrv->initProcess($params, $files, $data, $globalPrefix);
			$params['ready_files'] = $files;

			if ($wf->can($history, 'prepare_data')) {
				$context['params'] = $params;
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepare_data');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			$this->loggerSrv->addError('Error preparing data for EmailParsing workflow.', $thr);
			throw $thr;
		}
	}
}
