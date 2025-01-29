<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use App\Linker\Handler\XtmHandler;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/subscribers/xtm')]
class XtmController extends AbstractController
{
	private LoggerService $loggerSrv;
	private XtmHandler $xtmHandler;

	public function __construct(
		LoggerService $loggerSrv,
		XtmHandler $xtmHandler,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->xtmHandler = $xtmHandler;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
	}

	#[PublicEndpoint]
	#[Route(path: '/project-finished', name: 'subscriber_project_finished')]
	public function projectFinishedCallback(Request $request): Response
	{
		try {
			return $this->xtmHandler->proccessProjectFinished($request);

		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
			throw $thr;
		}
	}

	#[PublicEndpoint]
	#[Route(path: '/job-finished', name: 'subscriber_job_finished')]
	public function jobFinishedCallback(Request $request): Response
	{
		$response = new Response();
		try {
			$response = $this->xtmHandler->processJobFinished($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);

		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route(path: '/workflow-transition', name: 'subscriber_workflow_transition', methods: ['GET', 'POST'])]
	public function workflowTransitionCallback(Request $request): Response
	{
		$response = new Response();
		try {
			$response = $this->xtmHandler->processWorkflowTransition($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
		}

		return $response;
	}

	#[PublicEndpoint]
	#[Route(path: '/analysis-finished', name: 'subscriber_analysis_finished', methods: ['GET', 'POST'])]
	public function analysisFinishedCallback(Request $request): Response
	{

		$response = new Response();
		try {
			$response = $this->xtmHandler->processAnalysisFinished($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
		}

		return $response;
	}

	/**
	 * @return Response
	 *
	 * @throws \Exception
	 */
	#[PublicEndpoint]
	#[Route(path: '', name: 'xtm_project_subscriber', methods: ['POST'])]
	public function xtmProjectSubscriber(Request $request)
	{
		$response = new Response();
		try {
			$response = $this->xtmHandler->processXtmProjectSubscriber($request);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in Xtm Project subscriber.', $thr);
		}

		return $response;
	}
}
