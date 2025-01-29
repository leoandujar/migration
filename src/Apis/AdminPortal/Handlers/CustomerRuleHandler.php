<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\AdminPortal\Http\Response\CustomerRule\AvCustomerRuleResponse;
use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVCustomerRule;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\AVCustomerRuleRepository;
use App\Model\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class CustomerRuleHandler
{
	private EntityManagerInterface $em;
	private CustomerRepository $customerRepository;
	private AVCustomerRuleRepository $customerRuleRepository;

	public function __construct(
		EntityManagerInterface $em,
		CustomerRepository $customerRepository,
		AVCustomerRuleRepository $customerRuleRepository
	) {
		$this->em = $em;
		$this->customerRepository = $customerRepository;
		$this->customerRuleRepository = $customerRuleRepository;
	}

	public function processList(array $params): ApiResponse
	{
		$totalRows = $this->customerRuleRepository->getCountRows();
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge($params, ['start' => $paginationDto->from]);
		$sqlResponse = $this->customerRuleRepository->getList($dataQuery);
		$result = [];

		foreach ($sqlResponse as $customerRule) {
			$result[] = Factory::AvCustomerRuleDtoInstance($customerRule);
		}

		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		$customerRule = $this->em->getRepository(AVCustomerRule::class)->find($id);

		if (!$customerRule) {
			return new ErrorResponse(Response::HTTP_NOT_FOUND, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer_rule');
		}

		return new ApiResponse(data: Factory::avCustomerRuleDtoInstance($customerRule));
	}

	public function processCreate(array $params): ApiResponse
	{
		$workflow = $params['workflow_id'] ?? null;
		$customer = $this->customerRepository->find($params['customer_id']);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		if ($workflow) {
			$workflow = $this->em->getRepository(WFWorkflow::class)->find($workflow);
			if (!$workflow) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
			}
		}

		$customerRule = new AVCustomerRule();
		$customerRule
			->setCustomer($customer)
			->setWorkflow($workflow)
			->setName(strip_tags($params['name']))
			->setType(strip_tags($params['type']))
			->setEvent(strip_tags($params['event']))
			->setFilters($params['filters'] ?? [])
			->setParameters($params['parameters'] ?? []);

		$this->em->persist($customerRule);
		$this->em->flush();

		return new AvCustomerRuleResponse([
			'entities' => [$customerRule],
		]);
	}

	public function processUpdate(array $params): ApiResponse
	{
		$customerRule = $this->em->getRepository(AVCustomerRule::class)->find($params['id']);

		if (!$customerRule) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'rule');
		}

		$workflow = $params['workflow_id'] ?? null;
		if ($workflow) {
			$workflow = $this->em->getRepository(WFWorkflow::class)->find($workflow);
			if (!$workflow) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
			}
			$customerRule->setWorkflow($workflow);
		}

		if (!empty($params['filters'])) {
			$customerRule->setFilters($params['filters']);
		}

		if (!empty($params['parameters'])) {
			$customerRule->setParameters($params['parameters']);
		}

		if (!empty($params['event'])) {
			$customerRule->setEvent(strip_tags($params['event']));
		}

		if (!empty($params['type'])) {
			$customerRule->setType(strip_tags($params['type']));
		}
		if (!empty($params['name'])) {
			$customerRule->setName(strip_tags($params['name']));
		}

		$this->em->persist($customerRule);
		$this->em->flush();

		return new AvCustomerRuleResponse([
			'entities' => [$customerRule],
		]);
	}

	public function processDelete(string $id): ApiResponse
	{
		$customerRule =  $this->em->getRepository(AVCustomerRule::class)->find($id);

		if (!$customerRule) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'rule');
		}

		$this->em->remove($customerRule);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
