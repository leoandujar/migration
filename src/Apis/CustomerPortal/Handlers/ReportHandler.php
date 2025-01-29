<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\CustomerPortal\Services\ReportTypeService;
use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Connector\JsReport\JsReportConnector;
use App\Model\Entity\AVChart;
use App\Model\Entity\AVPivReportTemplateChart;
use App\Model\Entity\AVReportTemplate;
use App\Model\Entity\AVReportType;
use App\Model\Repository\AVReportTemplateRepository;
use App\Model\Repository\AVReportTypeRepository;
use App\Model\Repository\CategoryGroupRepository;
use App\Service\FileSystem\FileSystemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReportHandler extends BaseHandler
{
	private EntityManagerInterface $em;
	private JsReportConnector $jsReportConn;
	private FileSystemService $fileSystemSrv;
	private ReportTypeService $reportTypeSrv;
	private TokenStorageInterface $tokenStorage;
	private AVReportTypeRepository $avReportTypeRepo;
	private CategoryGroupRepository $categoryGroupRepo;
	private AVReportTemplateRepository $reportTemplateRepo;
	private AVReportTemplateRepository $avReportTemplateRepo;
	private RequestStack $requestStack;

	public function __construct(
		EntityManagerInterface $em,
		RequestStack $requestStack,
		JsReportConnector $jsReportConn,
		FileSystemService $fileSystemSrv,
		ReportTypeService $reportTypeSrv,
		TokenStorageInterface $tokenStorage,
		AVReportTypeRepository $avReportTypeRepo,
		CategoryGroupRepository $categoryGroupRepo,
		AVReportTemplateRepository $reportTemplateRepo,
		AVReportTemplateRepository $avReportTemplateRepo
	) {
		parent::__construct($requestStack, $em);
		$this->em = $em;
		$this->tokenStorage = $tokenStorage;
		$this->jsReportConn = $jsReportConn;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->reportTypeSrv = $reportTypeSrv;
		$this->avReportTypeRepo = $avReportTypeRepo;
		$this->categoryGroupRepo = $categoryGroupRepo;
		$this->reportTemplateRepo = $reportTemplateRepo;
		$this->avReportTemplateRepo = $avReportTemplateRepo;
		$this->requestStack = $requestStack;
	}

	public function processList(array $params): ApiResponse
	{
		$customer = $this->getCurrentCustomer();
		$user = $this->getCurrentUser();
		$targetList = $user->getCategoryGroups() ?? $customer->getCategoryGroups() ?? [];
		$reportTemplateList = [];
		if ($targetList) {
			$params['groups'] = $targetList;
			$reportTemplateList = $this->reportTemplateRepo->getSearch($params);
		}

		$result = [];
		foreach ($reportTemplateList as $reportTemplate) {
			$result[] = Factory::reportTemplateDtoInstance($reportTemplate);
		}

		return new ApiResponse(data: $result);
	}

	public function processGenerateReport(array $params): ApiResponse|BinaryFileResponse
	{
		$customer = $this->getCurrentCustomer();
		$reportTemplate = $this->avReportTemplateRepo->find($params['id']);

		if (!$reportTemplate) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_template');
		}

		$format = $params['format'] ?? $reportTemplate->getFormat();

		if (AVReportTemplate::EXPORT_FORMAT_PDF !== $format) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_UNSUPPORTED, ApiError::$descriptions[ApiError::CODE_UNSUPPORTED]);
		}
		if (!in_array($format, [AVReportTemplate::EXPORT_FORMAT_PDF, AVReportTemplate::EXPORT_FORMAT_EXCEL])) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'format');
		}

		$filters = $reportTemplate->getFilters();
		$predefinedData = $reportTemplate->getPredefinedData();

		if (isset($params['filters'])) {
			$filters = array_merge($filters, $params['filters']);
		}

		if (isset($params['predefined_data'])) {
			$predefinedData = array_merge($predefinedData, $params['predefined_data'], $filters);
		}

		$templateId = $reportTemplate->getTemplate();

		if (empty($templateId)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_EMPTY_TEMPLATE_ID, ApiError::$descriptions[ApiError::CODE_EMPTY_TEMPLATE_ID]);
		}
		$chartList = $params['report_types'] ?? $reportTemplate->getChartList();

		if (!$chartList) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_EMPTY_LIST, ApiError::$descriptions[ApiError::CODE_EMPTY_LIST]);
		}

		$chartObjList = [];

		foreach ($chartList as $currentChartPiv) {
			$chartCode = $chartObj = null;
			if ($currentChartPiv instanceof AVPivReportTemplateChart) {
				$chartObj = $currentChartPiv->getChart();
			} else {
				$chartObj = $this->avReportTypeRepo->findOneBy(['code' => $chartCode]);
			}
			if ($chartObj) {
				$chartObjList[] = $chartObj;
			}
		}

		if (!$chartObjList) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_EMPTY_LIST, ApiError::$descriptions[ApiError::CODE_EMPTY_LIST]);
		}
		$filters['customer_id'] = $customer->getId();
		$queryData = $this->fetchData($chartObjList, $filters);
		$reportData = array_merge($queryData, $predefinedData);

		return new ApiResponse(data: $reportData);
	}

	private function fetchData(array $chartList, array $filters)
	{
		$result = [];
		/** @var AVChart $chart */
		foreach ($chartList as $chart) {
			switch ($chart->getType()) {
				case AVReportType::CHART_TYPE_NONE:
					break;
				case AVReportType::CHART_TYPE_WIDGET:
					$filters['widget_id'] = $chart->getReportType()?->getCode();
					$result['charts']['widgets'][] = [
						'id' => $chart->getReportType()?->getCode(),
						'title' => $chart->getName(),
						'description' => $chart->getDescription(),
						'type' => $chart->getType(),
						'data' => $this->reportTypeSrv->processWidgets($filters),
					];
					break;
				default:
					$filters['graph_id'] = $chart->getReportType()?->getCode();
					$result['charts'][$chart->getCategory()][] = array_merge([
						'id' => $chart->getReportType()?->getCode(),
						'title' => $chart->getName(),
						'description' => $chart->getDescription(),
						'type' => $chart->getType(),
						'category' => $chart->getCategory(),
					], $this->reportTypeSrv->processCharts($filters));
			}
		}

		return $result;
	}

	private function getFormat(int $format): ?string
	{
		switch ($format) {
			case AVReportTemplate::EXPORT_FORMAT_PDF:
				return 'pdf';
			case AVReportTemplate::EXPORT_FORMAT_EXCEL:
				return 'xls';
		}

		return null;
	}
}
