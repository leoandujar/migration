<?php

namespace App\Apis\CustomerPortal\Controller;

use App\Service\LoggerService;
use App\Apis\Shared\Http\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\CustomerPortal\Handlers\ResourcesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route(path: '/resources')]
class ResourcesController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ResourcesHandler $resourcesHandler;

	public function __construct(
		ResourcesHandler $resourcesHandler,
		LoggerService $loggerSrv
	) {
		$this->resourcesHandler = $resourcesHandler;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
	}

	#[Route('/languages/{customerId}', name: 'cp_resources_languages_list', methods: ['GET'])]
	public function getLanguagesList(Request $request, ?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$all = 'true' === $request->query->get('all', 'false');

			$response = $this->resourcesHandler->processGetLanguagesList($customerId, $all);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting languages.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/services/{customerId}', name: 'cp_resources_services_list', methods: ['GET'])]
	public function getServicesList(Request $request, ?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}

			$all = 'true' === $request->query->get('all', 'false');

			$response = $this->resourcesHandler->processGetServicesList($customerId, $all);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting services.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/specializations/{customerId}', name: 'cp_resources_specializations_list', methods: ['GET'])]
	public function getSpecializationList(?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetSpecializationList($customerId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting specializations.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/priceprofiles/{customerId}', name: 'cp_resources_priceprofiles_list', methods: ['GET'])]
	public function getPriceprofileList(Request $request, ?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetPriceprofileList($customerId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting priceprofiles.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/settings/schema', name: 'cp_resources_settings_schema', methods: ['GET'])]
	public function getSchema(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processSchema($requestObj->getParams());
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting settings schema.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/settings/{customerId}', name: 'cp_resources_settings', methods: ['GET'])]
	public function getSettings(?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetSettingsByCustomer($customerId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting settings.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/deadlines/{customerId}', name: 'cp_resources_deadlines_prediction', methods: ['GET'])]
	public function getProcessGetDeadlinePrediction(?string $customerId = null): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetDeadlinePrediction($customerId);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting deadline prediction.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/offices', name: 'cp_resources_offices_list', methods: ['GET'])]
	public function getOfficesList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetOfficesList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting languages.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/timezones', name: 'cp_resources_timezones_list', methods: ['GET'])]
	public function timezoneList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$timezoneList = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, 'US');
			$response = new ApiResponse(data: $timezoneList);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error retrieving timezone list.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/countries', name: 'cp_resources_countries_list', methods: ['GET'])]
	public function getCountryList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetCountryList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting countries.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/countries/{id}/provinces', name: 'cp_resources_country_provinces_list', methods: ['GET'])]
	public function getProvincesList(Request $request, string $id): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetProvincesByCountry($id);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting provinces by country.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}

	#[Route('/reject-reasons', name: 'cp_resources_reject_reasons_list', methods: ['GET'])]
	public function getRejectReasonsList(Request $request): ApiResponse|ErrorResponse
	{
		try {
			$requestObj = new ApiRequest();
			if (!$requestObj->isValid()) {
				return $requestObj->getError();
			}
			$response = $this->resourcesHandler->processGetRejectReasonsList();
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error getting reject reasons.', $thr);
			$response = new ErrorResponse();
		}

		return $response;
	}
}
