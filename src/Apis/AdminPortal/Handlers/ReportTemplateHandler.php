<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Apis\Shared\Util\Factory;
use App\Model\Entity\AVPivReportTemplateChart;
use App\Model\Entity\AVReportTemplate;
use App\Model\Repository\AVChartRepository;
use App\Model\Repository\AVReportTemplateRepository;
use App\Model\Repository\CategoryGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ReportTemplateHandler
{
	private EntityManagerInterface $em;
	private CategoryGroupRepository $categoryGroupRepo;
	private AVReportTemplateRepository $reportTemplateRepo;
	private AVChartRepository $chartRepo;

	public function __construct(
		EntityManagerInterface $em,
		CategoryGroupRepository $categoryGroupRepo,
		AVReportTemplateRepository $reportTemplateRepo,
		AVChartRepository $chartRepo,
	) {
		$this->em = $em;
		$this->reportTemplateRepo = $reportTemplateRepo;
		$this->categoryGroupRepo = $categoryGroupRepo;
		$this->chartRepo = $chartRepo;
	}

	public function processList(array $params): ApiResponse
	{
		$reportTemplateList = $this->reportTemplateRepo->getSearch($params);

		$result = [];
		foreach ($reportTemplateList as $reportTemplate) {
			$result[] = Factory::reportTemplateDtoInstance($reportTemplate);
		}

		return new ApiResponse(data: $result);
	}

	public function retrieve(string $id): ApiResponse
	{
		$reportTemplate = $this->reportTemplateRepo->find($id);

		if (!$reportTemplate) {
			return new ErrorResponse(Response::HTTP_NOT_FOUND, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'report_template');
		}

		return new ApiResponse(data: Factory::reportTemplateDtoInstance($reportTemplate));
	}

	public function processCreate(array $params): ApiResponse
	{
		$reportTemplate = $this->reportTemplateRepo->findOneBy(['name' => trim($params['name'])]);
		if ($reportTemplate) {
			return new ErrorResponse(Response::HTTP_CONFLICT, ApiError::CODE_ENTITY_EXISTS, ApiError::$descriptions[ApiError::CODE_ENTITY_EXISTS]);
		}

		if (!in_array(
			$params['format'],
			[
				AVReportTemplate::EXPORT_FORMAT_PDF,
				AVReportTemplate::EXPORT_FORMAT_EXCEL,
			]
		)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'type');
		}

		$reportTemplate = new AVReportTemplate();
		$reportTemplate
			->setName(strip_tags($params['name']))
			->setFormat(strip_tags($params['format']));

		if (!empty($params['filters'])) {
			$reportTemplate->setFilters($params['filters']);
		}

		if (!empty($params['predefined_data'])) {
			$reportTemplate->setPredefinedData($params['predefined_data']);
		}

		if (!empty($params['category_groups'])) {
			foreach ($params['category_groups'] as $groupCode) {
				$cGroupObj = $this->categoryGroupRepo->findOneBy(['code' => $groupCode]);
				if (!$cGroupObj) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
			}
			$reportTemplate->setCategoryGroups($params['category_groups']);
		}

		if (!empty($params['template'])) {
			$reportTemplate->setTemplate($params['template']);
		}

		foreach (array_unique($params['charts']) as $chartId) {
			$chart = $this->chartRepo->find($chartId);
			if (!$chart) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'chart');
			}

			$reportTypeTemplatePiv = new AVPivReportTemplateChart();
			$reportTypeTemplatePiv
				->setTemplate($reportTemplate)
				->setChart($chart);
			$reportTemplate->addReportTypeList($reportTypeTemplatePiv);
		}
		$this->em->persist($reportTemplate);
		$this->em->flush();

		return new ApiResponse(data: Factory::reportTemplateDtoInstance($reportTemplate));
	}

	public function processUpdate(array $params): ApiResponse
	{
		$reportTemplate = $this->reportTemplateRepo->find($params['id']);
		if (!$reportTemplate) {
			return new ErrorResponse(
				Response::HTTP_NOT_FOUND,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'report_template'
			);
		}

		if (!empty($params['name'])) {
			$reportTemplate->setName(strip_tags($params['name']));
		}

		if (!empty($params['format'])) {
			if (!in_array(
				$params['format'],
				[
					AVReportTemplate::EXPORT_FORMAT_PDF,
					AVReportTemplate::EXPORT_FORMAT_EXCEL,
				]
			)) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'type');
			}
			$reportTemplate->setFormat(strip_tags($params['format']));
		}

		if (!empty($params['filters'])) {
			$reportTemplate->setFilters($params['filters']);
		}

		if (!empty($params['predefined_data'])) {
			$reportTemplate->setPredefinedData($params['predefined_data']);
		}

		if (!empty($params['category_groups'])) {
			foreach ($params['category_groups'] as $groupCode) {
				$cGroupObj = $this->categoryGroupRepo->findOneBy(['code' => $groupCode]);
				if (!$cGroupObj) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'category');
				}
			}
			$reportTemplate->setCategoryGroups($params['category_groups']);
		}

		if (!empty($params['template'])) {
			$reportTemplate->setTemplate($params['template']);
		}

		if (!empty($params['charts'])) {
			foreach ($reportTemplate->getChartList() as $item) {
				$this->em->remove($item);
			}
			$this->em->flush();
			foreach (array_unique($params['charts']) as $chartId) {
				$chart = $this->chartRepo->find($chartId);
				if (!$chart) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'chart');
				}
				$reportTypeTemplatePiv = new AVPivReportTemplateChart();
				$reportTypeTemplatePiv
					->setTemplate($reportTemplate)
					->setChart($chart);
				$reportTemplate->getChartList()->add($reportTypeTemplatePiv);
			}
		}

		$this->em->persist($reportTemplate);
		$this->em->flush();

		return new ApiResponse(data: Factory::reportTemplateDtoInstance($reportTemplate));
	}

	public function processDelete(string $id): ApiResponse
	{
		$reportTemplate = $this->reportTemplateRepo->find($id);
		if (!$reportTemplate) {
			return new ErrorResponse(
				Response::HTTP_NOT_FOUND,
				ApiError::CODE_NOT_FOUND,
				ApiError::$descriptions[ApiError::CODE_NOT_FOUND],
				'report_template'
			);
		}
		$this->em->remove($reportTemplate);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
