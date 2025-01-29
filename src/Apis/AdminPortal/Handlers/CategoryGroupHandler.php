<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\CategoryGroup;
use App\Model\Entity\WFWorkflow;
use App\Model\Repository\AVReportTemplateRepository;
use App\Model\Repository\CustomerRepository;
use App\Model\Repository\WorkflowRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\CategoryGroupRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;

class CategoryGroupHandler
{
	use UserResolver;
	private EntityManagerInterface $em;
	private WorkflowRepository $wfRepo;
	private CustomerRepository $customerRepo;
	private AVReportTemplateRepository $templateRepo;
	private CategoryGroupRepository $categoryGroupRepo;

	public function __construct(
		EntityManagerInterface $em,
		WorkflowRepository $wfRepo,
		CustomerRepository $customerRepo,
		AVReportTemplateRepository $templateRepo,
		CategoryGroupRepository $categoryGroupRepo
	) {
		$this->em = $em;
		$this->wfRepo = $wfRepo;
		$this->customerRepo = $customerRepo;
		$this->templateRepo = $templateRepo;
		$this->categoryGroupRepo = $categoryGroupRepo;
	}

	public function processGetList(array $params): ApiResponse
	{
		if (null !== $params['target']) {
			$target = intval($params['target']);
			if (!in_array($target, [CategoryGroup::TARGET_CHART, CategoryGroup::TARGET_REPORT_TEMPLATE, CategoryGroup::TARGET_AP_WORKFLOW])) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'target');
			}
		}

		$result = [];
		foreach ($this->categoryGroupRepo->getSearch($params) as $categoryGroup) {
			$result[] = Factory::categoryGroupDtoInstance($categoryGroup);
		}

		return new ApiResponse(data: $result);
	}

	public function processRetrieve(string $id): ApiResponse
	{
		/** @var CategoryGroup $categoryGroup */
		$categoryGroup = $this->categoryGroupRepo->find($id);

		if (!$categoryGroup) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
		}

		return new ApiResponse(data: Factory::categoryGroupDtoInstance($categoryGroup));
	}

	public function processAssignToCustomer(array $params): ApiResponse
	{
		$groupList = $params['groups'];
		$customer = $this->customerRepo->find($params['id']);
		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}
		foreach ($groupList as $groupCode) {
			$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => strtoupper($groupCode)]);
			if (!$categoryGroup) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
			}
		}
		$customer->setCategoryGroups($groupList);
		$this->em->persist($customer);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processAssignToTemplate(array $params): ApiResponse
	{
		$groupList = $params['groups'];
		$template = $this->templateRepo->find($params['id']);
		if (!$template) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
		}
		foreach ($groupList as $groupCode) {
			$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => strtoupper($groupCode)]);
			if (!$categoryGroup) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
			}
		}
		$template->setCategoryGroups($groupList);
		$this->em->persist($template);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processAssignToWorkflow(array $params): ApiResponse
	{
		$groupList = $params['groups'];
		/** @var WFWorkflow $workflow */
		$workflow = $this->wfRepo->find($params['id']);
		if (!$workflow) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'workflow');
		}
		foreach ($groupList as $groupCode) {
			$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => strtoupper($groupCode)]);
			if (!$categoryGroup) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
			}
			if (CategoryGroup::TARGET_AP_WORKFLOW !== $categoryGroup->getTarget()) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_GROUP_TARGET_INVALID, ApiError::$descriptions[ApiError::CODE_GROUP_TARGET_INVALID]);
			}
			$workflow->addCategoryGroup($categoryGroup);
		}
		$this->em->persist($workflow);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processCreate(array $params): ApiResponse
	{
		if (null !== $params['target']) {
			$target = intval($params['target']);
			if (!in_array($target, [CategoryGroup::TARGET_CHART, CategoryGroup::TARGET_REPORT_TEMPLATE, CategoryGroup::TARGET_AP_WORKFLOW])) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'target');
			}
		}
		$categoryGroup = $this->categoryGroupRepo->findOneBy(['code' => strtoupper($params['code']), 'target' => $params['target']]);

		if ($categoryGroup) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ROW_ALREADY_EXISTS, ApiError::$descriptions[ApiError::CODE_ROW_ALREADY_EXISTS]);
		}
		$categoryGroup = new CategoryGroup();
		$categoryGroup
			->setName($params['name'])
			->setCode(strtoupper($params['code']))
			->setTarget($params['target'])
			->setActive(boolval($params['active']));
		$this->em->persist($categoryGroup);
		$this->em->flush();

		return new ApiResponse(data: Factory::categoryGroupDtoInstance($categoryGroup));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$categoryGroup = $this->categoryGroupRepo->find($params['id']);

		if (!$categoryGroup) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
		}

		if (!empty($params['name'])) {
			$categoryGroup->setName($params['name']);
		}

		if (isset($params['active']) && null !== $params['active']) {
			$categoryGroup->setActive(boolval($params['active']));
		}

		if (!empty($params['target'])) {
			$target = intval($params['target']);
			if (!in_array($target, [CategoryGroup::TARGET_CHART, CategoryGroup::TARGET_REPORT_TEMPLATE, CategoryGroup::TARGET_AP_WORKFLOW])) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'target');
			}
			$categoryGroup->setTarget($params['target']);
		}

		$this->em->persist($categoryGroup);
		$this->em->flush();

		return new ApiResponse(data: Factory::categoryGroupDtoInstance($categoryGroup));
	}

	public function processDelete(array $params): ApiResponse
	{
		$categoryGroup = $this->categoryGroupRepo->find($params['id']);

		if (!$categoryGroup) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'group');
		}

		$this->em->remove($categoryGroup);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
