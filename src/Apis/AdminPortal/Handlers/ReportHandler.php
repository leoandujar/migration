<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVChart;
use App\Model\Entity\AVReportTemplate;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Repository\AVChartRepository;
use App\Service\FileSystem\FileSystemService;
use App\Model\Entity\AVPivReportTemplateChart;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Connector\JsReport\JsReportConnector;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\AVReportTemplateRepository;
use App\Model\Repository\CustomerRepository;
use App\Apis\CustomerPortal\Services\ReportTypeService;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportHandler
{
	private JsReportConnector $jsReportConn;
	private FileSystemService $fileSystemSrv;
	private ReportTypeService $reportTypeSrv;
	private AVReportTemplateRepository $reportTemplateRepo;
	private AVReportTemplateRepository $avReportTemplateRepo;
	private CustomerRepository $customerRepo;
	private AVChartRepository $chartRepo;

	public function __construct(
		JsReportConnector $jsReportConn,
		FileSystemService $fileSystemSrv,
		ReportTypeService $reportTypeSrv,
		AVChartRepository $chartRepo,
		AVReportTemplateRepository $reportTemplateRepo,
		AVReportTemplateRepository $avReportTemplateRepo,
		CustomerRepository $customerRepo
	) {
		$this->jsReportConn = $jsReportConn;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->reportTypeSrv = $reportTypeSrv;
		$this->reportTemplateRepo = $reportTemplateRepo;
		$this->avReportTemplateRepo = $avReportTemplateRepo;
		$this->customerRepo = $customerRepo;
		$this->chartRepo = $chartRepo;
	}

	public function processList(string $id): ApiResponse
	{
		$customer = $this->customerRepo->find($id);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$targetList = $customer->getCategoryGroups() ?? [];
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
		$customer = $this->customerRepo->find($params['customer_id']);

		if (!$customer) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'customer');
		}

		$reportTemplate = $this->avReportTemplateRepo->find($params['id']);

		if (!$reportTemplate) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'template');
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
		$chartList = $params['charts'] ?? $reportTemplate->getChartList();

		if (!$chartList) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_EMPTY_LIST, ApiError::$descriptions[ApiError::CODE_EMPTY_LIST]);
		}

		$chartObjList = [];

		foreach ($chartList as $currentChart) {
			$chartSlug = $chartObj = null;
			if ($currentChart instanceof AVPivReportTemplateChart) {
				$chartObj = $currentChart->getChart();
			} else {
				$chartObj = $this->chartRepo->findOneBy(['slug' => $chartSlug]);
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
		$customerData = [
			'customer_id' => $customer->getId(),
			'customer_name' => $customer->getName(),
		];

		$reportData = array_merge($queryData, $predefinedData, $customerData, $filters);

		if (isset($params['debug']) && $params['debug']) {
			return new ApiResponse(data: $reportData);
		}

		$renderResponse = $this->jsReportConn->render($templateId, $reportData);

		if (!$renderResponse->isSuccessfull()) {
			return new ErrorResponse(Response::HTTP_BAD_GATEWAY, ApiError::CODE_UNABLE_DOWNLOAD_FILE, ApiError::$descriptions[ApiError::CODE_UNABLE_DOWNLOAD_FILE]);
		}
		$fileBinary = $renderResponse->getRaw();
		$this->fileSystemSrv->createDirectory($this->fileSystemSrv->filesPath, 'reports_generated');
		$filePath = $this->fileSystemSrv->filesPath."/reports_generated/{$reportTemplate->getName()}.{$this->getFormat($reportTemplate->getFormat())}";
		if ($this->fileSystemSrv->createOrOverrideFile($filePath, $fileBinary)) {
			$response = new BinaryFileResponse($filePath);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

			return $response;
		}
	}

	private function fetchData(array $chartList, array $filters): array
	{
		$result = [];
		/** @var AVChart $chart */
		foreach ($chartList as $chart) {
			switch ($chart->getType()) {
				case AVChart::CHART_TYPE_NONE:
					break;
				case AVChart::CHART_TYPE_WIDGET:
					$filters['widget_id'] = $chart->getSlug();
					$result['charts']['widgets'][] = [
						'id' => $chart->getSlug(),
						'title' => $chart->getName(),
						'description' => $chart->getDescription(),
						'type' => $chart->getType(),
						'data' => $this->reportTypeSrv->processWidgets($filters),
					];
					break;
				default:
					$filters['graph_id'] = $chart->getSlug();
					$test = $this->reportTypeSrv->processCharts($filters);
					$result['charts'][$chart->getCategory()][] = array_merge([
						'id' => $chart->getSlug(),
						'title' => $chart->getName(),
						'description' => $chart->getDescription(),
						'type' => $chart->getType(),
						'category' => $chart->getCategory(),
						'options' => $chart->getOptions(),
					], $test);
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
