<?php

namespace App\Apis\AdminPortal\Controller;

use App\Apis\AdminPortal\Handlers\CommandHandler;
use App\Apis\AdminPortal\Http\Request\Commands\RunCommandRequest;
use App\Apis\Shared\Http\Request\ApiRequest;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/commands')]
class CommandController extends AbstractController
{
	private LoggerService $loggerSrv;
	private CommandHandler $commandHandler;

	public function __construct(
		CommandHandler $commandHandler,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		$this->commandHandler = $commandHandler;

		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_ADMIN_PORTAL);
	}

	#[Route('/list', name: 'ap_commands_list', methods: ['GET'])]
	public function list(): ApiResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->commandHandler->processList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting command list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/run', name: 'ap_commands_run', methods: ['POST'])]
	public function run(Request $request): ApiResponse
	{
		try {
			$requestObj = new RunCommandRequest($request->getPayload()->all());
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$response = $this->commandHandler->processRun($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error running command.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
