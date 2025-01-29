<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Util\Factory;
use App\Linker\Services\RedisClients;
use App\Model\Entity\SystemAccount;
use App\Model\Repository\ContactPersonRepository;
use App\Service\LoggerService;
use App\Model\Entity\CustomerInvoice;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\UtilsService;
use Stripe\Exception\ApiErrorException;
use App\Apis\Shared\Http\Error\ApiError;
use App\Connector\Stripe\StripeConnector;
use App\Service\FileSystem\FileSystemService;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\CustomerInvoiceRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Connector\CustomerPortal\CustomerPortalConnector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class InvoicesHandler extends BaseHandler
{
	private UtilsService $utilsSrv;
	private LoggerService $loggerSrv;
	private SessionInterface $session;
	private FileSystemService $fileSystemSrv;
	private StripeConnector $stripeConnector;
	private TokenStorageInterface $tokenStorage;
	private UrlGeneratorInterface $urlGenerator;
	private CustomerPortalConnector $clientConnector;
	private CustomerInvoiceRepository $invoiceRepository;
	private RedisClients $redisClients;
	private ContactPersonRepository $contactPersonRepo;
	private RequestStack $requestStack;
	private EntityManagerInterface $em;

	public function __construct(
		RequestStack $requestStack,
		RedisClients $redisClients,
		TokenStorageInterface $tokenStorage,
		UtilsService $utilsSrv,
		ContactPersonRepository $contactPersonRepo,
		CustomerPortalConnector $clientConnector,
		FileSystemService $fileSystemSrv,
		StripeConnector $stripeConnector,
		UrlGeneratorInterface $urlGenerator,
		LoggerService $loggerSrv,
		CustomerInvoiceRepository $invoiceRepository,
		EntityManagerInterface $em
	) {
		parent::__construct($requestStack, $em);
		$this->session = $requestStack->getSession();
		$this->utilsSrv = $utilsSrv;
		$this->tokenStorage = $tokenStorage;
		$this->invoiceRepository = $invoiceRepository;
		$this->clientConnector = $clientConnector;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->stripeConnector = $stripeConnector;
		$this->urlGenerator = $urlGenerator;
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_API_CLIENT_PORTAL);
		$this->redisClients = $redisClients;
		$this->contactPersonRepo = $contactPersonRepo;
		$this->requestStack = $requestStack;
	}

	public function processGetInvoices(array $dataRequest): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();

		if (isset($dataRequest['internal_status'])) {
			foreach ($dataRequest['internal_status'] as $status) {
				if (!in_array($status, [CustomerInvoice::INVOICE_STATUS_READY, CustomerInvoice::INVOICE_STATUS_NO_READY, CustomerInvoice::INVOICE_STATUS_SENT])) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
				}
			}
		}

		$officePlace = $this->getOfficeCurrentUser();
		if (empty($officePlace)) {
			return new ErrorResponse(
				Response::HTTP_BAD_REQUEST,
				ApiError::CODE_MANAGE_POLICY_EMPTY,
				ApiError::$descriptions[ApiError::CODE_MANAGE_POLICY_EMPTY]
			);
		}

		switch ($officePlace) {
			case SystemAccount::OFFICE_ONLY_RELATED:
				$dataRequest['contact_person_id'] = [$user->getId()];
				$dataRequest['customer_id'] = [$customer->getId()];
				break;
			case SystemAccount::OFFICE_DEPARTMENT:
				$dataRequest['customer_id'] = [$customer->getId()];
				$contactPersonList = $this->contactPersonRepo->getListBySystemAccount($officePlace);
				foreach ($contactPersonList as $cp) {
					$id = array_shift($cp);
					if (empty($dataRequest['requested_by']) || in_array($id, $dataRequest['requested_by'])) {
						$dataRequest['contact_person_id'][] = $id;
					}
				}
				break;
			case SystemAccount::OFFICE_OFFICE:
				$dataRequest['customer_id'] = [$customer->getId()];
				break;
			case SystemAccount::OFFICE_ALL_OFFICE:
				// Prevent both keys at the same time. Customers has more priority than contact persons.
				if (!empty($dataRequest['offices']) && !empty($dataRequest['requested_by'])) {
					unset($dataRequest['requested_by']);
				}
				$customerPerson = $user->getCustomersPerson();
				if (!$customerPerson) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_person');
				}
				$customers = $customerPerson->getCustomers();
				foreach ($customers as $cust) {
					if (empty($dataRequest['offices']) || in_array($cust->getId(), $dataRequest['offices'])) {
						$dataRequest['customer_id'][] = $cust->getId();
					}
				}
				break;
			default:
				return new ErrorResponse(
					Response::HTTP_BAD_REQUEST,
					ApiError::CODE_INVALID_VALUE,
					ApiError::$descriptions[ApiError::CODE_INVALID_VALUE],
					'manage_policy'
				);
		}
		if (empty($dataRequest['customer_id'])) {
			return new ErrorResponse(
				Response::HTTP_NOT_FOUND,
				ApiError::CODE_MISSING_PARAM,
				ApiError::$descriptions[ApiError::CODE_MISSING_PARAM],
				'customer_id'
			);
		}

		if (isset($dataRequest['sort_by'])) {
			$sortBy = match ($dataRequest['sort_by']) {
				'dueDate' => 'requiredPaymentDate',
				'idNumber' => 'finalNumber',
				'status' => 'paymentState',
				default => $dataRequest['sort_by'],
			};
			$dataRequest['sort_by'] = $sortBy;
		}

		$totalRows = $this->invoiceRepository->getCountSearchInvoices($dataRequest);
		$paginationDto = new PaginationDto($dataRequest['page'], $dataRequest['per_page'], $totalRows, $dataRequest['sort_order'], $dataRequest['sort_by']);
		$dataRequest['start'] = $paginationDto->from;
		$sqlResponse = $this->invoiceRepository->getSearchInvoices($dataRequest);

		$result = [];
		foreach ($sqlResponse as $invoice) {
			$result[] = Factory::invoiceDtoInstance($invoice);
		}
		$response = new DefaultPaginationResponse(
			[
				'entities' => $result,
			]
		);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processExportInvoices(array $dataRequest): BinaryFileResponse|ApiResponse
	{
		$invoicesResponse = $this->processGetInvoices($dataRequest);
		if ($invoicesResponse instanceof ErrorResponse) {
			return $invoicesResponse;
		}

		$arrayData = json_decode($invoicesResponse->getContent(), true);
		$projects = $arrayData['data'];
		if (empty($projects)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'project');
		}

		$flattenedData = array_map(function ($item) {
			return $this->flatten($item);
		}, $arrayData['data']);

		$serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
		$csvData = $serializer->encode($flattenedData, 'csv');

		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'invoice_files');
		$filePath = $this->fileSystemSrv->filesPath.'/invoice_files/invoices_info_'.uniqid().'.csv';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $csvData)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath).'.csv');
			$response->deleteFileAfterSend();

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function flatten($array, $prefix = ''): array
	{
		$result = [];
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = $result + $this->flatten($value, $prefix.$key.'.');
			} else {
				$result[$prefix.$key] = $value;
			}
		}

		return $result;
	}

	public function processGetInvoice(string $id): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		$invoice = $this->invoiceRepository->find($id);

		if (!$invoice) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'invoice');
		}

		if ($invoice->getCustomer()?->getId() !== $customer->getId()) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_NOT_ENOUGH_PERMISSIONS, ApiError::$descriptions[ApiError::CODE_NOT_ENOUGH_PERMISSIONS]);
		}

		return new ApiResponse(data: Factory::invoiceDtoInstance($invoice, true));
	}

	public function processGeneratePdf(array $dataRequest): BinaryFileResponse|ApiResponse
	{
		$invoiceIds = array_unique($dataRequest['invoiceIds']);
		$generatedInvoices = $this->generateInvoices($invoiceIds);

		if ($generatedInvoices instanceof ErrorResponse) {
			return $generatedInvoices;
		}

		if (1 === count($invoiceIds)) {
			return $this->handleSingleInvoice($generatedInvoices);
		}

		return $this->handleMultipleInvoices($generatedInvoices);
	}

	private function generateInvoices(array $invoiceIds): array|ErrorResponse
	{
		$generatedInvoices = [];

		foreach ($invoiceIds as $id) {
			$invoice = $this->invoiceRepository->find($id);
			if (!$invoice) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'invoice');
			}

			$this->loggerSrv->addInfo('GENERATING PDF', ['ID' => $id]);

			$generatedInvoice = $this->clientConnector->invoiceGeneratePdf($id);
			if (!$generatedInvoice->isSuccessfull()) {
				return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_XTRF_COMMUNICATION_ERROR, ApiError::$descriptions[ApiError::CODE_XTRF_COMMUNICATION_ERROR]);
			}

			$generatedInvoices[] = $generatedInvoice;
		}

		return $generatedInvoices;
	}

	private function handleSingleInvoice(array $generatedInvoices): BinaryFileResponse|ApiResponse
	{
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'invoices_files');
		$filePath = $this->fileSystemSrv->filesPath.'/invoices_files/invoice'.uniqid().'.pdf';
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $generatedInvoices[0]->getRaw())) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	private function handleMultipleInvoices(array $generatedInvoices): BinaryFileResponse|ApiResponse
	{
		$zip = new \ZipArchive();
		$zipPath = $this->fileSystemSrv->filesPath.'/invoices_files/invoice'.uniqid().'.zip';
		$filesToDelete = [];

		foreach ($generatedInvoices as $generatedInvoice) {
			$filePath = $this->createInvoiceFile($generatedInvoice);

			if ($filePath) {
				if (true !== $zip->open($zipPath, \ZipArchive::CREATE)) {
					return new ErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, ApiError::CODE_ZIP_ERROR, ApiError::$descriptions[ApiError::CODE_ZIP_ERROR]);
				}

				$zip->addFile($filePath, basename($filePath));
				$filesToDelete[] = $filePath;
			}
		}

		$zip->close();

		$this->deleteFiles($filesToDelete);

		$response = new BinaryFileResponse($zipPath);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'invoices.zip');

		return $response;
	}

	private function createInvoiceFile($generatedInvoice): ?string
	{
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'invoices_files');
		$filePath = $this->fileSystemSrv->filesPath.'/invoices_files/invoice'.uniqid().'.pdf';
		$fileBinary = $generatedInvoice->getRaw();

		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			return $filePath;
		}

		return null;
	}

	private function deleteFiles(array $filesToDelete): void
	{
		foreach ($filesToDelete as $filePath) {
			if (file_exists($filePath)) {
				unlink($filePath);
			}
		}
	}

	public function processCheckout(array $dataRequest): ApiResponse|ErrorResponse
	{
		$reference = $dataRequest['reference'] ?? null;
		$description = $dataRequest['description'] ?? 'Avantpage invoice payment';
		$userId = $this->getCurrentUser()->getId();
		$email = $this->getCurrentUser()->getEmail();
		$path = $dataRequest['path'] ?? '/invoices';

		$lines = [];

		foreach ($dataRequest['invoice_ids'] as $id) {
			$invoice = $this->invoiceRepository->find($id);
			if (!$invoice) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'invoice');
			}

			if (CustomerInvoice::INVOICE_STATUS_SENT !== $invoice->getState()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
			}

			if (CustomerInvoice::PAYMENT_STATUS_UNPAID !== $invoice->getPaymentState()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'payment_status');
			}
			$lines[] = [
				'price_data' => [
					'currency' => $invoice->getCurrency()->getIsoCode(),
					'product_data' => [
						'name' => $invoice->getFinalNumber(),
					],
					'unit_amount_decimal' => round($invoice->getTotalNetto(), 2) * 100,
				],
				'quantity' => 1,
			];
		}

		try {
			$stripeSession = $this->stripeConnector->createSession(
				$reference,
				$lines,
				$description,
				$path,
				$userId,
				$email
			);

			return new ApiResponse(
				data: [
					'clientSecret' => $stripeSession->client_secret,
				]
			);
		} catch (ApiErrorException $ex) {
			$this->loggerSrv->addError('Error processing Stripe checkout', $ex);

			return new ErrorResponse($ex->getCode(), ApiError::CODE_PAYMENT_ERROR, ApiError::$descriptions[ApiError::CODE_PAYMENT_ERROR]);
		}
	}

	public function processCheckoutStatus(array $dataRequest): ApiResponse|ErrorResponse
	{
		try {
			$stripeSession = $this->stripeConnector->retrieveSession($dataRequest['session_id']);

			return new ApiResponse(
				data: [
					'status' => $stripeSession->status,
					'email' => $stripeSession->customer_details?->email,
				]
			);
		} catch (ApiErrorException $ex) {
			$this->loggerSrv->addError('Error retrieving Stripe checkout status', $ex);

			return new ErrorResponse($ex->getCode(), ApiError::CODE_PAYMENT_ERROR, ApiError::$descriptions[ApiError::CODE_PAYMENT_ERROR]);
		}
	}

	public function processPayment(string $id): ApiResponse|ErrorResponse
	{
		$invoice = $this->invoiceRepository->find($id);
		if (!$invoice) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'invoice');
		}

		if (CustomerInvoice::INVOICE_STATUS_SENT !== $invoice->getState()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
		}

		if (CustomerInvoice::PAYMENT_STATUS_UNPAID !== $invoice->getPaymentState()) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'payment_status');
		}

		$amount = intval($invoice->getTotalNetto() * 10 ** 2);
		$currency = $invoice->getCurrency()->getIsoCode();
		$description = sprintf('Payment invoice %s', $invoice->getFinalNumber());

		try {
			$stripeResponse = $this->stripeConnector->createPaymentIntent($id, $amount, $currency, $description);
		} catch (ApiErrorException $ex) {
			$this->loggerSrv->addError('Error processing Stripe payment', $ex);

			return new ErrorResponse($ex->getCode(), ApiError::CODE_PAYMENT_ERROR, ApiError::$descriptions[ApiError::CODE_PAYMENT_ERROR]);
		}

		return new ApiResponse(
			data: [
				'invoice' => $invoice->getId(),
				'clientSecret' => $stripeResponse->client_secret,
			]
		);
	}

	public function processPaymentResult(array $dataRequest): ApiResponse|ErrorResponse
	{
		$invoice = $this->invoiceRepository->find($dataRequest['id']);
		if (!$invoice) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'invoice');
		}

		return new ApiResponse(
			data: [
				'success' => boolval($dataRequest['success']),
				'id' => $invoice->getId(),
				'idNumber' => $invoice->getFinalNumber(),
				'internalStatus' => strtolower($invoice->getState()),
				'status' => strtolower($invoice->getPaymentState()),
				'paidValue' => $invoice->getPaidValue(),
				'totalNetto' => $invoice->getTotalNetto(),
			]
		);
	}
}
