<?php

namespace App\Connector\Xtrf;

use App\Connector\Xtrf\Dto\CustomerDto;
use App\Connector\Xtrf\Request\Customers\UpdateCustomerRequest;
use App\Connector\Xtrf\Request\Quote\UpdateQuoteCustomFieldsRequest;
use App\Service\LoggerService;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use App\Connector\Xtrf\Request\Request;
use GuzzleHttp\Exception\GuzzleException;
use App\Connector\Xtrf\Response\Response;
use App\Connector\Xtrf\Dto\CustomerPersonDto;
use App\Connector\Xtrf\Response\Tasks\TaskResponse;
use App\Connector\Xtrf\Request\Quote\GetQuoteRequest;
use App\Connector\Xtrf\Request\Services\GetAllRequest;
use App\Connector\Xtrf\Request\Tasks\StartTaskRequest;
use App\Connector\Xtrf\Request\Users\GetSingleRequest;
use App\Connector\Xtrf\Response\Users\GetListResponse;
use App\Connector\Xtrf\Response\Quote\GetQuoteResponse;
use App\Connector\Xtrf\Response\Users\GetSingleResponse;
use App\Connector\Xtrf\Request\Browser\GetBrowserRequest;
use App\Connector\Xtrf\Request\Services\GetActiveRequest;
use App\Connector\Xtrf\Request\Tasks\ProgressTaskRequest;
use App\Connector\Xtrf\Response\Services\ServiceResponse;
use App\Connector\Xtrf\Request\Projects\GetProjectRequest;
use App\Connector\Xtrf\Response\Browser\GetBrowserResponse;
use App\Connector\Xtrf\Request\Customers\GetCustomerRequest;
use App\Connector\Xtrf\Response\Projects\GetProjectResponse;
use App\Connector\Xtrf\Request\Projects\CreateProjectRequest;
use App\Connector\Xtrf\Request\Projects\UploadTaskFileRequest;
use App\Connector\Xtrf\Response\Customers\GetCustomerResponse;
use App\Connector\Xtrf\Request\Customers\GetSinginTokenRequest;
use App\Connector\Xtrf\Response\Projects\CreateProjectResponse;
use App\Connector\Xtrf\Request\Dictionaries\GetDictionaryRequest;
use App\Connector\Xtrf\Request\Projects\UploadProjectFileRequest;
use App\Connector\Xtrf\Request\Customers\GetCustomerPersonRequest;
use App\Connector\Xtrf\Response\Dictionaries\GetDictionaryResponse;
use App\Connector\Xtrf\Response\Projects\UploadProjectFileResponse;
use App\Connector\Xtrf\Request\Invoices\CreateInvoicePaymentRequest;
use App\Connector\Xtrf\Request\Projects\CreateAdditionalTaskRequest;
use App\Connector\Xtrf\Request\Quote\UpdateQuoteInstructionsRequest;
use App\Connector\Xtrf\Response\Customers\GetCustomerPersonResponse;
use App\Connector\Xtrf\Request\Customers\CreateCustomerPersonRequest;
use App\Connector\Xtrf\Request\Customers\DeleteCustomerPersonRequest;
use App\Connector\Xtrf\Request\Customers\UpdateCustomerPersonRequest;
use App\Connector\Xtrf\Request\Projects\AdditionalInstructionsRequest;
use App\Connector\Xtrf\Request\Projects\UpdateProjectCustomFieldsRequest;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Connector\Xtrf\Request\Projects\ProjectAdditionalContactsFieldsRequest;
use App\Connector\Xtrf\Request\Subscriptions\SubscriptionCreateRequest;
use App\Connector\Xtrf\Request\Subscriptions\SubscriptionDeleteRequest;
use App\Connector\Xtrf\Request\Subscriptions\SubscriptionListRequest;

class XtrfConnector
{
	private string $url;
	private string $authToken;
	private ?GuzzleClient $client = null;
	private LoggerService $loggerSrv;

	public function __construct(ParameterBagInterface $bag, LoggerService $loggerSrv)
	{
		$this->url = $bag->get('app.xtrf.api_url');
		$this->authToken = $bag->get('app.xtrf.auth_token');
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	protected function sendRequest(Request $request, string $responseClass, bool $skipLogs = false): ?Response
	{
		try {
			$headers = ['X-AUTH-ACCESS-TOKEN' => $this->authToken];
			$headers = array_merge($headers, $request->getHeaders());
			if (Request::TYPE_JSON === $request->getType()) {
				$options['body'] = json_encode($request->getParams());
			} elseif (Request::TYPE_FORM === $request->getType()) {
				$options['form_params'] = $request->getParams();
			} else {
				$options['multipart'] = $request->getParams();
			}
			$options['headers'] = $headers;
			if (null === $this->client) {
				$this->client = new GuzzleClient([
					RequestOptions::CONNECT_TIMEOUT => $request->getTimeout(),
					RequestOptions::READ_TIMEOUT => $request->getTimeout(),
					RequestOptions::TIMEOUT => $request->getTimeout(),
					RequestOptions::HTTP_ERRORS => false,
				]);
			}
			$response = $this->client->request(
				$request->getRequestMethod(),
				"$this->url{$request->getRequestUri()}",
				$options
			);
			$responseString = $response->getBody()->getContents();
			$responseBody = json_decode($responseString, true);
			if (empty($responseBody)) {
				$responseBody = [];
			}
			switch ($response->getStatusCode()) {
				case 400:
					$this->loggerSrv->addError(sprintf('An error has happened sending request to xtrf: %s, request: %s, response: %s', $request->getRequestUri(), json_encode($request->getParams()), $responseString));
					break;
				case 200:
					break;
				default:
					$this->loggerSrv->addInfo(sprintf('Response from xtrf: %s, request: %s, response: %s: %s', $request->getRequestUri(), json_encode($request->getParams()), $response->getStatusCode(), $responseString));
					break;
			}
			if (!$skipLogs) {
				$this->loggerSrv->addInfo(sprintf('Succesful request to xtrf: %s, request: %s, response: %s', $request->getRequestUri(), json_encode($request->getParams()), $responseString));
			}

			return new $responseClass($response->getStatusCode(), $responseBody);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error sending request to xtrf', $thr);
			throw $thr;
		}
	}

	// #########################  BROWSER ############################################################

	/**
	 * Returns data from browser request.
	 *
	 * @return ?Response
	 */
	public function getDataBrowser($params): ?Response
	{
		$request = new GetBrowserRequest($params);
		try {
			return $this->sendRequest($request, GetBrowserResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getDataBrowser to Home Api', $thr);
		}

		return null;
	}

	// ######################### END BROWSER #############################################################
	// ################################################################################################
	// #########################  USERS ###########################################################
	/**
	 * Returns list of simple users representations.
	 *
	 * @return ?Response
	 */
	public function getUsers(): ?Response
	{
		$request = new SubscriptionListRequest();
		try {
			return $this->sendRequest($request, GetListResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request  getUsers to Home Api', $thr);
		}

		return null;
	}

	/**
	 * Returns user details.
	 *
	 * @return ?Response
	 */
	public function getSingleUser($userId): ?Response
	{
		$request = new GetSingleRequest($userId);
		try {
			return $this->sendRequest($request, GetSingleResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getSingleUser to Home Api', $thr);
		}

		return null;
	}

	// #########################  END USERS ###########################################################
	// ################################################################################################
	// #########################  SERVICES ############################################################
	/**
	 * Returns services list.
	 *
	 * @return ?Response
	 */
	public function getAllServices(): ?Response
	{
		$request = new GetAllRequest();
		try {
			return $this->sendRequest($request, ServiceResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getAllServices to Home Api', $thr);
		}

		return null;
	}

	/**
	 * Returns active services list.
	 *
	 * @return ServiceResponse
	 */
	public function getActiveServices(): ?Response
	{
		$request = new GetActiveRequest();
		try {
			return $this->sendRequest($request, ServiceResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getActiveServices to Home Api', $thr);
		}

		return null;
	}

	// ######################### END SERVICES ############################################################
	// ###################################################################################################
	// #########################  DICTIONARIES ###########################################################
	/**
	 * Returns user details.
	 *
	 * @return GetDictionaryResponse
	 */
	public function getDictionary($dictionaryId, $activeOnly = false): ?Response
	{
		$request = new GetDictionaryRequest($dictionaryId, $activeOnly);
		try {
			return $this->sendRequest($request, GetDictionaryResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getDictionary to Home Api', $thr);
		}

		return null;
	}

	// #########################  END DICTIONARIES ####################################################
	// ################################################################################################

	// #########################  PROJECTS ############################################################

	/**
	 * @return GetProjectResponse
	 */
	public function getProject(string $projectId): ?Response
	{
		$request = new GetProjectRequest($projectId);
		try {
			return $this->sendRequest($request, GetProjectResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getProject to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return CreateProjectResponse
	 */
	public function createProject(array $params): ?Response
	{
		$request = new CreateProjectRequest($params);
		try {
			return $this->sendRequest($request, CreateProjectResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createProject to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function createAdditionalTaskRequest(string $projectId, array $params): ?Response
	{
		$request = new CreateAdditionalTaskRequest($projectId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createAdditionalTaskRequest to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function uploadTaskFile(string $taskId, array $params): ?Response
	{
		$request = new UploadTaskFileRequest($taskId, $params);
		try {
			return $this->sendRequest($request, Response::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request uploadTaskFile to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return UploadProjectFileResponse
	 */
	public function uploadProjectFile(array $params): ?Response
	{
		$request = new UploadProjectFileRequest($params);
		try {
			return $this->sendRequest($request, UploadProjectFileResponse::class, true);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request uploadProjectFile to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function updateProjectCustomFields(string $projectId, array $params): ?Response
	{
		$request = new UpdateProjectCustomFieldsRequest($projectId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateProjectCustomFields to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function updateQuoteCustomFields(string $quoteId, array $params): ?Response
	{
		$request = new UpdateQuoteCustomFieldsRequest($quoteId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateQuoteCustomFields to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function additionalContactPerson(string $projectId, array $params): ?Response
	{
		$request = new ProjectAdditionalContactsFieldsRequest($projectId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request additionalContactPerson to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function additionalInstructions(string $projectId, array $params): ?Response
	{
		$request = new AdditionalInstructionsRequest($projectId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request additionalInstructions to Home Api', $thr);
		}

		return null;
	}

	// #########################  QUOTES ############################################################

	/**
	 * @return GetQuoteResponse
	 */
	public function getQuote(string $quoteId): ?Response
	{
		$request = new GetQuoteRequest($quoteId);
		try {
			return $this->sendRequest($request, GetQuoteResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getQuote to Home Api', $thr);
		}

		return null;
	}

	// #########################  END QUOTES ####################################################
	// ################################################################################################

	// #########################  END PROJECTS ####################################################
	// ################################################################################################

	// ######################## CUSTOMERS ##############################################################
	// ################################################################################################

	/**
	 * @return GetCustomerPersonResponse
	 */
	public function getCustomerPerson(string $personId): ?Response
	{
		$request = new GetCustomerPersonRequest($personId);
		try {
			return $this->sendRequest($request, GetCustomerPersonResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getCustomerPerson to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return GetCustomerResponse
	 */
	public function getCustomer(string $customerId): ?Response
	{
		$request = new GetCustomerRequest($customerId);
		try {
			return $this->sendRequest($request, GetCustomerResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getCustomer to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return GetCustomerPersonResponse
	 */
	public function createCustomerPerson(CustomerPersonDto $customerPersonDto): ?Response
	{
		$request = new CreateCustomerPersonRequest($customerPersonDto);
		try {
			return $this->sendRequest($request, GetCustomerPersonResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createCustomerPerson to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return GetCustomerPersonResponse
	 */
	public function updateCustomerPerson(CustomerPersonDto $customerPersonDto): ?Response
	{
		$request = new UpdateCustomerPersonRequest($customerPersonDto);
		try {
			return $this->sendRequest($request, GetCustomerPersonResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateContactPerson to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return GetCustomerPersonResponse
	 */
	public function updateCustomer(CustomerDto $customerDto): ?Response
	{
		$request = new UpdateCustomerRequest($customerDto);
		try {
			return $this->sendRequest($request, GetCustomerResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request updateCustomer to Home Api', $thr);
		}

		return null;
	}

    /**
     * @param string $personId
     * @return ?Response
     */
	public function deleteCustomerPerson(string $personId): ?Response
	{
		$request = new DeleteCustomerPersonRequest($personId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request deleteCustomerPerson to Home Api', $thr);
		}

		return null;
	}

	// ######################## END CUSTOMERS #############################################################
	// ################################################################################################

	// ######################## TASK #################################################################
	// ################################################################################################
	/**
	 * @return TaskResponse|null
	 */
	public function startTask(string $taskId): ?Response
	{
		$request = new StartTaskRequest($taskId);
		try {
			return $this->sendRequest($request, TaskResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request startTask to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return TaskResponse|null
	 */
	public function getTaskProgress(string $taskId): ?Response
	{
		try {
			$request = new ProgressTaskRequest($taskId);

			return $this->sendRequest($request, TaskResponse::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request getTaskProgress to Home Api', $thr);
		}

		return null;
	}

	// ######################## END TASKS #############################################################
	// ################################################################################################

	// ######################## INVOICES #################################################################
	// ################################################################################################

	/**
	 * @return ?Response
	 */
	public function createInvoicePayment(string $invoiceId, array $params): ?Response
	{
		try {
			$request = new CreateInvoicePaymentRequest($invoiceId, $params);

			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createInvoicePayment to Home Api', $thr);
		}

		return null;
	}

	// ######################## END INVOICES #############################################################
	// ################################################################################################

	// #################### START QUOTES ############################################################
	// ###################################################################################################
	/**
	 * @return mixed|null
	 */
	public function quoteStartTasks($quoteID): ?Response
	{
		try {
			$request = new StartTaskRequest($quoteID);

			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request generate a single use sign-in token to Home Api', $thr);
		}

		return null;
	}

	/**
	 * @return ?Response
	 */
	public function updateQuoteInstructions(string $quoteId, array $params): ?Response
	{
		$request = new UpdateQuoteInstructionsRequest($quoteId, $params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request UpdateQuoteInstructionsRequest to Home Api', $thr);
		}

		return null;
	}

	// #################### END QUOTES ############################################################
	// ###################################################################################################

	// ####################### GET ONE TIME SIGN-IN TOKEN ################################################
	// ###################################################################################################

	public function getSingInToken($username): ?Response
	{
		try {
			$request = new GetSinginTokenRequest($username);

			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request generate a single use sign-in token to Home Api', $thr);
		}

		return null;
	}

	// ####################### END GET ONE TIME SIGN-IN TOKEN ############################################
	// ###################################################################################################

	// ####################### SUBSCRIPTIONS ################################################
	// ###################################################################################################
	public function getSubscriptions(): ?Response
	{
		$request = new SubscriptionListRequest();
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request  getSubscriptions to Home Api', $thr);
		}

		return null;
	}

	public function createSubscription(array $params): ?Response
	{
		$request = new SubscriptionCreateRequest($params);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request createSubscriptions to Home Api', $thr);
		}

		return null;
	}

	public function deleteSubscription(string $subscriptionId): ?Response
	{
		$request = new SubscriptionDeleteRequest($subscriptionId);
		try {
			return $this->sendRequest($request, Response::class);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Error: sending request deleteSubscriptions to Home Api', $thr);
		}

		return null;
	}

	// ####################### END SUBSCRIPTIONS #########################################################
	// ###################################################################################################
}
