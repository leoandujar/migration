<?php

namespace App\Linker\Handler;

use App\Service\LoggerService;
use App\Model\Entity\WFWorkflow;
use App\Workflow\Services\XtmProject\Start;
use App\Linker\Services\LinkProjectService;
use App\Model\Repository\WorkflowRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Workflow\Services\XtmGithub\Start as GithubWF;

class XtmHandler
{
	private LoggerService $loggerSrv;
	private LinkProjectService $linkProjectService;
	private WorkflowRepository $workflowRepository;
	private Start $xtmSrv;
	private GithubWF $githubWF;

	public function __construct(
		LoggerService $loggerSrv,
		LinkProjectService $linkProjectService,
		WorkflowRepository $workflowRepository,
		Start $xtmSrv,
		GithubWF $githubWF,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->linkProjectService = $linkProjectService;
		$this->workflowRepository = $workflowRepository;
		$this->xtmSrv = $xtmSrv;
		$this->githubWF = $githubWF;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_LINKERS);
	}

	public function proccessProjectFinished(Request $request): Response
	{
		try {
			$xtmCustomerId = 'xtmCustomerId';
			$xtmProjectId = 'xtmProjectId';
			$internalCustomerID = 'internalCustomerID';
			$testFlag = 'test';
			$workflow = 'workflow';
			$response = new Response();
			$params = $request->query->all();
			if (isset($params[$testFlag])) {
				$this->loggerSrv->addInfo('TEST request: '.print_r($params, 1));

				return $response;
			}
			do {
				if (!isset($params[$xtmCustomerId]) || !isset($params[$xtmProjectId])) {
					$msg = 'Required parameters was not found in the request';
					$this->loggerSrv->addError($msg);
					$response = new Response($msg, Response::HTTP_BAD_REQUEST);
					break;
				}
				$this->linkProjectService->link($params[$xtmProjectId]);
			} while (0);

			if (isset($params[$internalCustomerID]) && isset($params[$workflow])) {
				$wf = $this->workflowRepository->findOneBy(['name' => $params['workflow']]);
				$wfParams = clone $wf->getParameters();
				$githubWFParams = $wfParams->getParams();
				$internalCustomerId = $githubWFParams['customerId'];
				if ($params[$internalCustomerID] === $internalCustomerId) {
					$wfParams->setParams(array_merge(
						$githubWFParams,
						['project_id' => $params[$xtmProjectId]]
					));
					$this->githubWF->Run($params['workflow'], $wfParams);
				}
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
			throw $thr;
		}

		return $response;
	}

	public function processJobFinished(Request $request): Response
	{
		$xtmCustomerId = 'xtmCustomerId';
		$xtmProjectId = 'xtmProjectId';
		$xtmJobId = 'xtmJobId';
		$response = new Response();
		try {
			do {
				$params = $request->query->all();
				if (!isset($params[$xtmCustomerId]) || !isset($params[$xtmProjectId]) || !isset($params[$xtmJobId])) {
					$msg = 'Required parameters was not found in the request';
					$this->loggerSrv->addError($msg);
					$response = new Response($msg, Response::HTTP_BAD_REQUEST);
					break;
				}
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
		}

		return $response;
	}

	public function processWorkflowTransition(Request $request): Response
	{
		$xtmCustomerId = 'xtmCustomerId';
		$xtmProjectId = 'xtmProjectId';
		$response = new Response();
		try {
			do {
				$params = $request->query->all();
				if (!isset($params[$xtmCustomerId]) || !isset($params[$xtmProjectId])) {
					$msg = 'Required parameters was not found in the request';
					$this->loggerSrv->addError($msg);
					$response = new Response($msg, Response::HTTP_BAD_REQUEST);
					break;
				}
			} while (0);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error in callback.', $thr);
		}

		return $response;
	}

	public function processAnalysisFinished(Request $request): Response
	{
		$xtmCustomerId = 'xtmCustomerId';
		$xtmProjectId = 'xtmProjectId';
		$response = new Response();
		try {
			do {
				$params = $request->query->all();
				if (!isset($params[$xtmCustomerId]) || !isset($params[$xtmProjectId])) {
					$msg = 'Required parameters was not found in the request';
					$this->loggerSrv->addError($msg);
					$response = new Response($msg, Response::HTTP_BAD_REQUEST);
					break;
				}
			} while (0);
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
	public function processXtmProjectSubscriber(Request $request)
	{
		/**
		 * @var WFWorkflow $wf
		 */
		$wf = $this->workflowRepository->findOneBy(['name' => 'xtm-create-project']);
		if (null === $wf) {
			return new Response('Workflow xtm_project is not found');
		}
		$content = $request->getContent();
		$this->loggerSrv->addInfo("Request received from remote client=> $content");
		$jsonRequest = json_decode($content, true);
		$params = $jsonRequest;
		if (null !== $wf->getParameters()) {
			$params = array_merge($wf->getParameters()->getParams(), $params);
		}
		$tmp = clone $wf->getParameters();
		$tmp->setParams($params);
		$this->xtmSrv->Run('xtm-create-project', $tmp);

		return new Response('OK');
	}
}
