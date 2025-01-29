<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Model\Entity\AVChart;
use App\Model\Entity\CategoryGroup;
use App\Apis\Shared\Util\UtilsService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Model\Entity\ContactPerson;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\AVChartRepository;
use App\Model\Repository\CategoryGroupRepository;
use App\Apis\CustomerPortal\Services\ReportTypeService;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVDashboard;
use App\Apis\Shared\Handlers\BaseHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardHandler extends BaseHandler
{
	private UtilsService $utilsService;
	private ReportTypeService $reportTypeSrv;
	private TokenStorageInterface $tokenStorage;
	private AVChartRepository $reportChartRepo;
	private CategoryGroupRepository $categoryGroupRepository;
	private RequestStack $requestStack;
	private EntityManagerInterface $em;

	public function __construct(
		UtilsService $utilsService,
		ReportTypeService $reportTypeSrv,
		TokenStorageInterface $tokenStorage,
		RequestStack $requestStack,
		AVChartRepository $reportChartRepo,
		CategoryGroupRepository $categoryGroupRepository,
		EntityManagerInterface $em
	) {
		parent::__construct($requestStack, $em);
		$this->tokenStorage = $tokenStorage;
		$this->utilsService = $utilsService;
		$this->reportTypeSrv = $reportTypeSrv;
		$this->reportChartRepo = $reportChartRepo;
		$this->categoryGroupRepository = $categoryGroupRepository;
		$this->requestStack = $requestStack;
		$this->em = $em;
	}

	private function checkChartPermission(string $chartId): ErrorResponse|bool
	{
		$chartPermissions = $this->processPermissions(true);

		if (!isset($chartPermissions->getDataResponse()['data'][$chartId])) {
			return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_CHART_FORBIDDEN, ApiError::$descriptions[ApiError::CODE_CHART_FORBIDDEN]);
		}

		return true;
	}

	/**
	 * If params $indexById=true the keys of result will be the chart id.
	 */
	public function processPermissions(bool $indexById = false): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customer = $this->getCurrentCustomer();
		$userChartGroups = $user->getCategoryGroups() ?? [];
		$customerChartGroups = $customer->getCategoryGroups() ?? [];
		$targetList = count($userChartGroups) ? $userChartGroups : $customerChartGroups;
		$result = [];

		foreach ($targetList as $groupCode) {
			/** @var CategoryGroup $group */
			$group = $this->em->getRepository(CategoryGroup::class)->findOneBy(['code' => $groupCode, 'active' => true, 'target' => CategoryGroup::TARGET_CHART]);
			if ($group instanceof CategoryGroup) {
				$chartsList = $group->getCharts();
				/** @var AVChart $chart */
				foreach ($chartsList as $chart) {
					if ($chart->getActive() && null !== $chart->getReportType()) {
						if (!$indexById) {
							$result[] = [
								'id' => $chart->getId(),
								'code' => $chart->getSlug(),
								'type' => $chart->getType(),
								'category' => $chart->getCategory(),
								'size' => $chart->getSize(),
							];
							continue;
						}
						$result[$chart->getId()][] = [
							'id' => $chart->getId(),
							'code' => $chart->getSlug(),
							'type' => $chart->getType(),
							'category' => $chart->getCategory(),
							'size' => $chart->getSize(),
						];
					}
				}
			}
		}

		return new ApiResponse(data: $result);
	}

	public function processWidgets(array $dataRequest): ApiResponse
	{
		$slug = $dataRequest['slug'];

		$chart = $this->em->getRepository(AVChart::class)->findOneBy(['slug' => $slug]);
		if (!$chart) {
			return new ErrorResponse(Response::HTTP_NOT_FOUND, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'graph');
		}

		$permission = $this->checkChartPermission($chart->getId());
		if (true !== $permission) {
			return $permission;
		}

		$customer = $this->getCurrentCustomer();
		$dataRequest['customer_id'] = $customer->getId();
		$dataRequest['widget_id'] = $chart->getId();
		$result = $this->reportTypeSrv->processWidgets($dataRequest);

		return new ApiResponse(data: $result);
	}

	public function processGraphs(array $dataRequest): ApiResponse
	{
		$graphId = $dataRequest['graph_id'];
		$permission = $this->checkChartPermission($graphId);
		if (true !== $permission) {
			return $permission;
		}

		$chart = $this->em->getRepository(AVChart::class)->find($graphId);
		if (!$chart) {
			return new ErrorResponse(Response::HTTP_NOT_FOUND, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'graph');
		}

		$customer = $this->getCurrentCustomer();
		$dataRequest['customer_id'] = $customer->getId();

		$result = $this->reportTypeSrv->processCharts($dataRequest);

		return new ApiResponse(data: $result);
	}

	public function processList(): ApiResponse
	{
		$user = $this->getCurrentUser();
		$customerCharts = $user->getDashboard();
		$permissions = $this->processPermissions();

		$permissions = array_map(function ($chart) {
			return $chart['id'];
		}, $permissions->getDataResponse()['data']);

		$result = [];
		foreach ($customerCharts as $chart) {
			if (in_array($chart->getAvChart()->getId(), $permissions)) {
				$result[] = Factory::avDashboardDtoInstance($chart);
			}
		}

		return new ApiResponse(data: $result);
	}

	public function processAdd(array $dataSent): ApiResponse
	{
		/** @var ContactPerson $user */
		$user = $this->getCurrentUser();
		$graphList = $dataSent['graphs'];
		$chartPermissions = $this->processPermissions(true);
		foreach ($user->getDashboard() as $dashboardItem) {
			$this->em->remove($dashboardItem);
		}
		$user->getDashboard()->clear();
		foreach ($graphList as $graph) {
			$chart = $this->em->getRepository(AVChart::class)->find($graph['id']);
			if (!$chart) {
				return new ErrorResponse(Response::HTTP_NOT_FOUND, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'graph_id');
			}

			if (empty($chartPermissions->getDataResponse()['data'][$chart->getId()])) {
				return new ErrorResponse(Response::HTTP_FORBIDDEN, ApiError::CODE_CHART_FORBIDDEN, ApiError::$descriptions[ApiError::CODE_CHART_FORBIDDEN], 'graph_id');
			}

			$avDashboard = (new AVDashboard())
				->setAvChart($chart)
				->setContactPerson($user)
				->setOptions($graph['options'] ?? []);
			$this->em->persist($avDashboard);
			$user->getDashboard()->add($avDashboard);
			$this->em->persist($user);
		}
		$this->em->flush();

		return $this->processList();
	}

	public function processGallery(): ApiResponse
	{
		$user = $this->getCurrentUser();
		$permission = $this->processPermissions();

		$dashboardCharts = array_map(function (AVDashboard $chart) {
			return $chart->getAvChart()->getId();
		}, $user->getDashboard()->toArray());

		$result = [];
		foreach ($permission->getDataResponse()['data'] as $chart) {
			if (in_array($chart['type'], [AVChart::CHART_TYPE_WIDGET, AVChart::CHART_TYPE_TABLE, AVChart::CHART_TYPE_NONE])) {
				continue;
			}
			$avChart = $this->em->getRepository(AVChart::class)->find($chart['id']);
			$result[] = [
				'graph' => Factory::avChartDtoInstance($avChart),
				'id' => $avChart->getId(),
				'active' => in_array($chart['id'], $dashboardCharts),
			];
		}

		return new ApiResponse(data: $result);
	}
}
