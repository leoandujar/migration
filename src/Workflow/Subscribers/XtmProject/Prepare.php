<?php

namespace App\Workflow\Subscribers\XtmProject;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Prepare implements EventSubscriberInterface
{
	private LoggerService $loggerSrv;
	private Registry $registry;
	private EntityManagerInterface $em;

	/**
	 * Prepare constructor.
	 */
	public function __construct(
		LoggerService $loggerSrv,
		Registry $registry,
		EntityManagerInterface $em
	) {
		$this->loggerSrv = $loggerSrv;
		$this->registry  = $registry;
		$this->em        = $em;
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
		$this->loggerSrv->setSubcontext(LoggerService::LOGGER_SUB_CONTEXT_WF_XTM_PROJECT);
	}

	public static function getSubscribedEvents(): array
	{
		return [
			'workflow.xtm_project.completed.initialized' => 'prepare',
		];
	}

	public function prepare(Event $event)
	{
		try {
			$history = $event->getSubject();
			$context = $history->getContext();
			$wf      = $this->registry->get($history, 'xtm_project');
			$client  = new Client(['base_uri' => $context['base_url']]);
			if (isset($context['posts'])) {
				foreach ($context['posts'] as $post) {
					if (array_key_exists('priority', $post)) {
						$context['priority'] = true;
					}
					$filtered = str_replace($context['filter_path'], '', $post['download_path']);
					$rsp      = $client->get(sprintf('%s%s', $context['source_path'], $filtered));
					if (!array_key_exists('project_params', $context)) {
						$context['project_params'] = [];
					}
					$context['project_params'][] = json_decode($rsp->getBody()->getContents(), true);
				}
			}
			if ($wf->can($history, 'prepared')) {
				$history->setContext($context);
				if (!$this->em->isOpen()) {
					$this->em = new EntityManager($this->em->getConnection(), $this->em->getConfiguration());
				}
				$this->em->persist($history);
				$this->em->flush();
				$wf->apply($history, 'prepared');
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->alert($event, $thr);
			throw $thr;
		}
	}
}
