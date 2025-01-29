<?php

namespace App\Apis\CustomerPortal\Services;

use App\Model\Entity\AVChart;
use App\Model\Repository\AVChartRepository;
use App\Model\Entity\Project;
use App\Apis\Shared\Util\UtilsService;
use App\Model\Repository\DashboardRepository;

class ReportTypeService
{
	private UtilsService $utilsSrv;
	private AVChartRepository $chartRepo;
	private DashboardRepository $dashboardRepo;

	public const TIMELINE_MONTH = 'month';
	public const TIMELINE_QUARTER = 'quarter';
	public const TIMELINE_YEAR = 'year';

	public function __construct(
		UtilsService $utilsSrv,
		AVChartRepository $chartRepo,
		DashboardRepository $dashboardRepo,
	) {
		$this->utilsSrv = $utilsSrv;
		$this->chartRepo = $chartRepo;
		$this->dashboardRepo = $dashboardRepo;
	}

	public function processWidgets(array $data)
	{
		$filters = [];
		$dateType = $data['relative_date'] ?? null;
		$groupBy = $data['group_by'] ?? null;
		$between = $data['between'] ?? null;
		$filters['customerId'] = $data['customer_id'];

		if ($dateType) {
			$dateResult = $this->utilsSrv->arrayDateYearsOrQuarters($dateType);
			$filters['startDate'] = $dateResult['startDate'];
			$filters['endDate'] = $dateResult['endDate'];
		}

		if (isset($data['between']) && 2 === count($data['between'])) {
			$filters['startDate'] = $between[0];
			$filters['endDate'] = $between[1];
		}

		if ($groupBy) {
			$filters['groupBy'] = $groupBy;
			if (!isset($data['between'])) {
				$dateResult = $this->utilsSrv->arrayDateTimeline($groupBy);
				$filters['startDate'] = $dateResult['startDate'];
				$filters['endDate'] = $dateResult['endDate'];
			}
		}

		$filters = array_merge($filters, $data);
		$chart = $this->chartRepo->find($data['widget_id']);
		$reportType = $chart->getReportType();

		if (AVChart::CHART_TYPE_WIDGET === $chart->getType()) {
			$function = trim($reportType->getFunctionName());
			if (!empty($function)) {
				return $this->dashboardRepo->$function($filters);
			}

			$function = 'widget'.ucfirst($reportType->getCode());
			$this->utilsSrv->arrayKeysToCamel($function);
			if (method_exists(self::class, $function)) {
				return $this->{$function}($filters);
			}
		}

		return null;
	}

	public function processCharts(array $data)
	{
		$filters = [];
		$dateType = $data['relative_date'] ?? null;
		$groupBy = $data['group_by'] ?? null;
		$between = $data['between'] ?? null;
		$filters['customerId'] = $data['customer_id'];

		if ($dateType) {
			$dateResult = $this->utilsSrv->arrayDateYearsOrQuarters($dateType);
			$filters['startDate'] = $dateResult['startDate'];
			$filters['endDate'] = $dateResult['endDate'];
		}

		if (isset($data['between']) && 2 === count($data['between'])) {
			$filters['startDate'] = $between[0];
			$filters['endDate'] = $between[1];
		}

		if ($groupBy) {
			$filters['groupBy'] = $groupBy;
			if (!isset($data['between'])) {
				$dateResult = $this->utilsSrv->arrayDateTimeline($groupBy);
				$filters['startDate'] = $dateResult['startDate'];
				$filters['endDate'] = $dateResult['endDate'];
			}
		}

		if (isset($data['filters'])) {
			$filters = array_merge($filters, $data['filters']);
		}
		$chart = $this->chartRepo->find($data['graph_id']);
		$reportType = $chart->getReportType();
		$filters['reportType'] = $reportType;
		$filters['chart'] = $chart;
		if (AVChart::CHART_TYPE_WIDGET !== $chart->getType()) {
			$functionChart = 'chart'.ucfirst($reportType->getCode());
			$functionTable = 'table'.ucfirst($reportType->getCode());
			$this->utilsSrv->arrayKeysToCamel($functionChart);
			$this->utilsSrv->arrayKeysToCamel($functionTable);
			if (method_exists(self::class, $functionChart)) {
				return $this->{$functionChart}($filters);
			}
			if (method_exists(self::class, $functionTable)) {
				return $this->{$functionTable}($filters);
			}
		}

		return null;
	}

	private function widgetSatisfactionScore(array $filters)
	{
		$totalClosedProjects = $this->dashboardRepo->getTotalClosedProjects($filters)['totalProjects'] ?? 0;
		$totalComplaint = $this->dashboardRepo->getFeedbackComplaint($filters)['totalComplaint'] ?? 0;
		$result = 100;
		if (0 !== $totalClosedProjects) {
			$result = $this->utilsSrv->amountFormat(100 - ($totalComplaint * 100) / $totalClosedProjects);
		}

		return $result;
	}

	private function chartWordsLanguage(array $filters): ?array
	{
		$result = [];
		$drilldown = [];
		$queryResultTaskCatCharge = $this->dashboardRepo->getWordsLanguageTaskCatCharge($filters);
		$queryResultTaskCharge = $this->dashboardRepo->getWordsLanguageTaskCharge($filters);
		foreach ($queryResultTaskCatCharge as $key => &$item) {
			if (isset($queryResultTaskCharge[$key])) {
				$item['total'] += $queryResultTaskCharge[$key]['total'];
				unset($queryResultTaskCharge[$key]);
			}
		}
		$queryResultTaskCatCharge = array_merge($queryResultTaskCatCharge, $queryResultTaskCharge);
		usort($queryResultTaskCatCharge, function ($a, $b) {
			if ($a['total'] == $b['total']) {
				return 0;
			}

			return $a['total'] < $b['total'] ? 1 : -1;
		});
		$others = [];
		$drillOthers = [];
		$i = 0;
		foreach ($queryResultTaskCatCharge as $currentElement) {
			$currentTotal = $currentElement['total'];

			if ($i < 10) {
				$result[] = [
					'name' => $currentElement['langName'],
					'y' => $currentTotal,
				];
			} else {
				if ($i < 25) {
					$drilldown[] = [
						$currentElement['langName'],
						$currentTotal,
					];
				} else {
					$totalDrillOthers = $drillOthers[1] ?? 0;
					$totalDrillOthers += $currentTotal;
					$drillOthers = [
						'Others',
						$totalDrillOthers,
					];
				}

				$totalOthers = $others['y'] ?? 0;
				$totalOthers += $currentTotal;
				$others = [
					'name' => 'Others',
					'y' => $totalOthers,
					'drilldown' => 'Words',
				];
			}
			++$i;
		}
		if (count($result)) {
			$result[] = $others;
		}
		if (count($drilldown)) {
			$drilldown[] = $drillOthers;
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				['name' => 'Words',
					'data' => $result, ],
			],
			'drilldown' => [
				[
					'name' => 'Words',
					'id' => 'Others',
					'data' => $drilldown,
				],
			],
		];
	}

	private function isEmptySeriesData(array $data): bool
	{
		$isEmpty = true;
		foreach ($data as $item) {
			if (isset($item['y']) && 0 != $item['y']) {
				$isEmpty = false;
				break;
			}
		}

		return $isEmpty;
	}

	public function chartProjectsStatus(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getProjectsStatus($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$result['categories'] = $categories;
			$timeLineData = [
				Project::STATUS_OPEN => array_fill_keys($categories, null),
				Project::STATUS_CANCELLED => array_fill_keys($categories, null),
				Project::STATUS_CLOSED => array_fill_keys($categories, null),
			];
			foreach ($queryResult as $data) {
				/** @var \DateTime $date */
				$date = trim($data['date']);
				$status = $data['status'];
				$timeLineData[$status]["$date"] += $data['total'];
			}

			array_walk($timeLineData, function ($data, $status) use (&$result) {
				$filteredData = array_filter(array_values($data));
				if (!empty($filteredData)) {
					$result['series'][] = [
						'name' => $status,
						'data' => array_values($data),
					];
				}
			});
		}

		return $result;
	}

	public function chartProjectsHistorical(array $filters): ?array
	{
		return $this->internalProjectsHistorical($filters);
	}

	public function chartWordsBreakdown(array $filters): ?array
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getWordsBreakdown($filters);
		foreach ($queryResult as $key => $total) {
			if (!empty($total)) {
				$name = match ($key) {
					'new_words' => 'New words',
					'fuzzy' => 'Fuzzy matches',
					'leveraged' => '100% matches',
					default => 'unknown',
				};
				$result[] = [
					'name' => $name,
					'y' => floatval($total),
				];
			}
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Words',
					'data' => $result,
				],
			],
		];
	}

	public function chartSpendService(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResultTaskCatCharge = $this->dashboardRepo->getSpendPerServiceTaskCatCharge($filters);
		$queryResultTaskCharge = $this->dashboardRepo->getSpendPerServiceTaskCharge($filters);
		foreach ($queryResultTaskCharge as $item) {
			$queryResultTaskCatCharge[] = $item;
		}
		$categories = $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']);
		$result['categories'] = $categories;
		$spendData = [];
		foreach ($queryResultTaskCatCharge as $data) {
			/** @var \DateTime $date */
			$date = trim($data['date']);
			$type = $data['type'];
			if (!isset($spendData[$type])) {
				$spendData[$type] = array_fill_keys($categories, null);
			}
			$spendData[$type]["$date"] += $data['total'];
		}
		array_walk($spendData, function ($data, $status) use (&$result) {
			$filteredData = array_filter(array_values($data));
			if (!empty($filteredData)) {
				$result['series'][] = [
					'name' => $status,
					'data' => array_values($data),
				];
			}
		});

		return $result;
	}

	public function chartSpendHistorical(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getSpendHistorical($filters);
		$series = [];
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, 0);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"] += $data['total'];
			}

			array_walk($quartersData, function ($data, $quarter) use (&$series) {
				$series[] = [
					'name' => $quarter,
					'y' => $data,
				];
			});
		}

		$isEmptySeriesData = $this->isEmptySeriesData($series);

		if (!$isEmptySeriesData) {
			$result['series'][] = [
				'name' => 'Projects',
				'data' => $series,
			];
		}

		return $result;
	}

	public function chartInvoicesOverdue(array $filters): ?array
	{
		$queryResult = $this->dashboardRepo->getInvoicesOverdue($filters);
		$result = [];
		foreach ($queryResult as $key => $total) {
			$name = match ($key) {
				'over_30' => '30 days overdue',
				'over_60' => '60 days overdue',
				'over_90' => '90 days overdue',
				default => '',
			};
			if ($total > 0) {
				$result[] = [
					'name' => $name,
					'y' => $total,
				];
			}
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Invoices',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendTmsavings(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getSpendTmsavings($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$result['categories'] = $categories;
			$timeLineData = [
				'TM Saving' => array_fill_keys($categories, null),
				'Total Spent' => array_fill_keys($categories, null),
			];
			foreach ($queryResult as $data) {
				/** @var \DateTime $date */
				$date = trim($data['date']);
				$timeLineData['Total Spent']["$date"] = $this->utilsSrv->amountNumberFormat($data['totalAgreed']);
				$timeLineData['TM Saving']["$date"] = $this->utilsSrv->amountNumberFormat($data['tmSaving']);
			}
			array_walk($timeLineData, function ($data, $name) use (&$result) {
				$filteredData = array_filter(array_values($data));
				if (!empty($filteredData)) {
					$result['series'][] = [
						'name' => $name,
						'data' => array_values($data),
					];
				}
			});
		}

		return $result;
	}

	private function chartTimelinesScore(array $filters)
	{
		$queryResult = $this->dashboardRepo->getTimelinesScore($filters);
		$processedQueryResult = [
			'Earlier' => ['total' => 0, 'status' => 'Earlier'],
			'OnTime' => ['total' => 0, 'status' => 'OnTime'],
			'Late' => ['total' => 0, 'status' => 'Late'],
		];
		foreach ($queryResult as $dataResult) {
			$currStatus = $this->utilsSrv->getTimelineStatus(trim($dataResult['status']));
			$processedQueryResult[$currStatus]['total'] += $dataResult['total'];
		}
		$totalTasks = $this->dashboardRepo->getTotalTaskByCustomer($filters)['totalTasks'] ?? 0;
		$result = [];
		foreach ($processedQueryResult as $data) {
			if (0 != $data['total'] && 0 != $totalTasks) {
				$result[] = [
					'name' => trim($data['status']),
					'y' => $this->utilsSrv->amountNumberFormat($data['total']),
				];
			}
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Projects',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendCostcenter(array $filters)
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getSpendCostcenter($filters);
		foreach ($queryResult as $index => $data) {
			$position = $index + 1;
			$result[] = [
				'name' => '' !== $data['costCenter'] ? $data['costCenter'] : 'Without Cost Center',
				'originalName' => $data['costCenter'],
				'total' => $data['total'],
				'y' => $this->utilsSrv->amountNumberFormat($data['volume']),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Spend',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendRequester(array $filters)
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getSpendRequester($filters);
		foreach ($queryResult as $data) {
			$result[] = [
				'name' => $data['name'],
				'total' => $data['total'],
				'y' => $this->utilsSrv->amountNumberFormat($data['volume']),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Spend',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendDepartment(array $filters)
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getSpendDepartment($filters);

		foreach ($queryResult as $data) {
			$result[] = [
				'name' => $data['departmentName'] ?: 'Others',
				'total' => $data['total'],
				'y' => $this->utilsSrv->amountNumberFormat($data['volume']),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Spend',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendProjectsRush(array $filters)
	{
		return $this->internalProcessingRush($filters);
	}

	private function chartProjectsRush(array $filters)
	{
		return $this->internalProcessingRush($filters);
	}

	private function chartProjectsMinimum(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$series = [];
		$isEmptyData = true;
		$queryResult = $this->dashboardRepo->getProjectsMinimum($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
				'volume' => 0,
			]);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"]['total'] += $data['total'];
				$quartersData["$date"]['volume'] += $data['volume'];
				if (isset($data['total']) && 0 != $data['total']) {
					$isEmptyData = false;
				}
			}

			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series) {
					$series[] = [
						'name' => $dateName,
						'volume' => $this->utilsSrv->amountFormat($data['volume']),
						'y' => $data['total'],
					];
				});
				$result['series'][] = [
					'name' => 'Minimum',
					'data' => $series,
				];
			}
		}

		return $result;
	}

	private function internalProcessingRush(array $filters): array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$series = [];
		$isEmptyData = true;
		$queryResult = $this->dashboardRepo->getProjectsRush($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			/** @var AVChart $chart */
			$chart = $filters['chart'];
			if (!$chart) {
				return $result;
			}

			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
				'volume' => 0,
			]);
			foreach ($queryResult as $data) {
				$total = $data['total'];
				$volume = $data['volume'];
				$date = trim($data['date']);
				$quartersData["$date"]['total'] += $total;
				$quartersData["$date"]['volume'] += $volume;
				if ($chart->getReturnY() && isset($data[$chart->getReturnY()]) && 0 != $data[$chart->getReturnY()]) {
					$isEmptyData = false;
				}
			}

			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series, $chart) {
					$series[] = [
						'name' => $dateName,
						'volume' => $this->utilsSrv->amountFormat($data['volume']),
						'y' => $data[$chart->getReturnY()],
					];
				});

				$result['series'][] = [
					'name' => 'Rush',
					'data' => $series,
				];
			}
		}

		return $result;
	}

	private function internalProjectsHistorical(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getProjectsHistorical($filters);
		$series = [];
		$isEmptyData = true;

		/** @var AVChart $chart */
		$chart = $filters['chart'];
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
				'volume' => 0,
			]);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"]['total'] += $data['total'];
				$quartersData["$date"]['volume'] += $data['volume'];
				if ($chart->getReturnY() && isset($data[$chart->getReturnY()]) && 0 != $data[$chart->getReturnY()]) {
					$isEmptyData = false;
				}
			}
			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series, $chart) {
					$series[] = [
						'name' => "$dateName",
						'volume' => $this->utilsSrv->amountNumberFormat($data['volume']),
						'y' => $data[$chart->getReturnY()],
					];
				});
				$result['series'][] = [
					'name' => 'Total',
					'data' => $series,
				];
			}
		}

		return $result;
	}

	private function chartSpendProjectsRushComparison(array $filters)
	{
		$rushData = $this->dashboardRepo->getProjectsRush($filters);
		$projectData = $this->dashboardRepo->getSpendHistorical($filters);
		$rushTotal = 0;
		$projectTotal = 0;
		foreach ($rushData as $rush) {
			$rushTotal += $rush['volume'];
		}
		foreach ($projectData as $project) {
			$projectTotal += $project['total'];
		}

		$nonRushData = $this->utilsSrv->amountNumberFormat($projectTotal - $rushTotal);

		if (0 === $rushTotal && 0 === $nonRushData) {
			$series = [];
		} else {
			$series = [
				[
					'name' => 'Projects',
					'data' => [
						[
							'name' => 'Rush',
							'y' => $rushTotal,
						],
						[
							'name' => 'Non-rush',
							'y' => $nonRushData,
						],
					],
				],
			];
		}

		return [
			'series' => $series,
		];
	}

	public function chartTasksRushLanguage(array $filters): ?array
	{
		$result = [];

		/** @var AVChart $chart */
		$chart = $filters['chart'];
		$queryResult = $this->dashboardRepo->getTasksRushLanguage($filters);
		foreach ($queryResult as $data) {
			$result[] = [
				'name' => $data['language'],
				'y' => floatval($data[$chart->getReturnY()]),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Tasks',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendProjectsMinimum(array $filters)
	{
		return $this->internalProcessingRush($filters);
	}

	private function chartSpendProjectsMinimumComparison(array $filters)
	{
		$projectMinData = $this->dashboardRepo->getProjectsMinimum($filters);
		$projectHistoricalData = $this->dashboardRepo->getProjectsHistorical($filters);
		$minTotal = 0;
		$projectTotal = 0;
		foreach ($projectMinData as $minimum) {
			$minTotal += $minimum['volume'];
		}
		foreach ($projectHistoricalData as $project) {
			$projectTotal += $project['volume'];
		}

		$minProjectData = $this->utilsSrv->amountNumberFormat($minTotal);
		$nonMinProjectData = $this->utilsSrv->amountNumberFormat($projectTotal - $minTotal);

		if (0 == $minProjectData && 0 == $nonMinProjectData) {
			$series = [];
		} else {
			$series = [
				[
					'name' => 'Projects',
					'data' => [
						[
							'name' => 'Minimum projects',
							'y' => $minProjectData,
						],
						[
							'name' => 'Non-minimum projects',
							'y' => $nonMinProjectData,
						],
					],
				],
			];
		}

		return [
			'series' => $series,
		];
	}

	private function chartProjectsMinimumLanguage(array $filters)
	{
		$result = [];

		/** @var AVChart $chart */
		$chart = $filters['chart'];
		$queryResult = $this->dashboardRepo->getProjectsMinimumLanguages($filters);

		foreach ($queryResult as $data) {
			$result[] = [
				'name' => $data['language'],
				'y' => ($chart->getReturnY() && $data[$chart->getReturnY()]) ? floatval($data[$chart->getReturnY()]) : 0,
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Project Minimum',
					'data' => $result,
				],
			],
		];
	}

	private function tableSpendServicesHours(array $filters)
	{
		$queryResult = $this->dashboardRepo->getSpendPerServiceHours($filters);
		$dataResult = $totalsResult = $headers = [];
		foreach ($queryResult as $data) {
			$lang = $data['language'];
			$activity = $data['activity'];
			$tasks = $data['tasks'];
			$hours = $data['hours'];
			$cost = $data['cost'];
			if (!in_array($activity, $headers)) {
				$headers[] = $activity;
			}
			$columns = ['tasks', 'hours', 'cost'];
			$dataResult[$lang][$activity] = [
				'tasks' => $tasks,
				'hours' => $this->utilsSrv->amountFormat($hours, 0),
				'cost' => $this->utilsSrv->amountFormat($cost),
			];
			if (!isset($totalsResult[$activity])) {
				$totalsResult[$activity] = [
					'tasks' => 0,
					'hours' => 0,
					'cost' => 0,
				];
			}

			$totalsResult[$activity]['cost'] += $cost;
			$totalsResult[$activity]['hours'] += $hours;
			$totalsResult[$activity]['tasks'] += $tasks;
		}

		return [
			'data' => $dataResult,
			'headers' => $headers,
			'columns' => $columns,
			'totals' => $totalsResult,
		];
	}

	private function chartProjectsGenre(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		/** @var AVChart $chart */
		$chart = $filters['chart'];
		$queryResult = $this->dashboardRepo->getProjectsGenre($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$result['categories'] = $categories;
			$timeLineData = [];
			foreach ($queryResult as $data) {
				$genre = $data['genre'] ?? 'Unknown';
				if (!isset($timeLineData[$genre])) {
					$timeLineData[$genre] = array_fill_keys($categories, null);
				}
				/** @var \DateTime $date */
				$date = trim($data['date']);
				$timeLineData[$genre]["$date"] += $data[$chart->getReturnY()];
			}
			array_walk($timeLineData, function ($data, $status) use (&$result) {
				$filteredData = array_filter(array_values($data));
				if (!empty($filteredData)) {
					$result['series'][] = [
						'name' => $status,
						'data' => array_values($data),
					];
				}
			});
		}

		return $result;
	}

	private function chartWordsHistorical(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$series = [];
		$isEmptyData = true;

		$queryResult = $this->dashboardRepo->getWordsHistorical($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
			]);

			/** @var AVChart $chart */
			$chart = $filters['chart'];
			if (!$chart) {
				return $result;
			}

			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				if (!isset($quartersData[$date])) {
					continue;
				}
				$quartersData["$date"]['total'] += $data['total'];
				if ($chart->getReturnY() && isset($data[$chart->getReturnY()]) && 0 != $data[$chart->getReturnY()]) {
					$isEmptyData = false;
				}
			}

			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series, $chart) {
					$series[] = [
						'name' => $dateName,
						'y' => $data[$chart->getReturnY()],
					];
				});

				$result['series'][] = [
					'name' => 'Words',
					'data' => $series,
				];
			}
		}

		return $result;
	}

	public function chartSpendComparison(array $filters): ?array
	{
		$result = [
			'categories' => [],
		];

		$sentStartDate = $filters['startDate'];
		if (!empty($filters['startDate']) && $filters['groupBy']) {
			$rest = '-1 year';
			$st = (new \DateTime($filters['startDate']))->modify($rest)->format('Y-m-d H:i:s');
			$filters['startDate'] = $st;
		}
		$queryResult = $this->dashboardRepo->getSpendHistorical($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($sentStartDate, $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($sentStartDate, $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($sentStartDate, $filters['endDate']),
			};

			$convertedCatories = [];
			$convertedQueryResult = [];
			$quartersData = [];

			foreach ($queryResult as $data) {
				$convertedQueryResult[$data['date']] = $data;
			}
			for ($i = 0; $i < count($categories) - 1; ++$i) {
				$currentDataSplitted = explode(' ', $categories[$i]);
				$currentQ = $currentDataSplitted[0];
				$currentYear = $currentDataSplitted[1] ?? '';
				$previousYear = $currentYear - 1;
				$previousQ = $currentQ;
				$convertedCatories[] = trim("$previousYear $previousQ-$currentYear $currentQ");
				$prevValue = $convertedQueryResult[trim("$previousQ $previousYear")]['total'] ?? 0;
				$curValue = $convertedQueryResult[trim("$currentQ $currentYear")]['total'] ?? 0;
				if ($prevValue) {
					$prevValue = $this->utilsSrv->amountNumberFormat($prevValue);
				}
				if ($curValue) {
					$curValue = $this->utilsSrv->amountNumberFormat($curValue);
				}
				$quartersData['Previous Period'][] = $prevValue;
				$quartersData['Current Period'][] = $curValue;
			}
			$result['categories'] = $convertedCatories;

			array_walk($quartersData, function ($data, $quarter) use (&$series) {
				$filteredData = array_filter(array_values($data));
				if (!empty($filteredData)) {
					$series[] = [
						'name' => $quarter,
						'data' => array_values($data),
					];
				}
			});
		}

		$result['series'] = $series ?? [];

		return $result;
	}

	public function chartProjectsComparisonYear(array $filters): ?array
	{
		$result = [
			'categories' => [],
		];

		$sentStartDate = $filters['startDate'];
		if (!empty($filters['startDate']) && $filters['groupBy']) {
			$rest = '-1 year';
			$st = (new \DateTime($filters['startDate']))->modify($rest)->format('Y-m-d H:i:s');
			$filters['startDate'] = $st;
		}
		$queryResult = $this->dashboardRepo->getProjectsHistorical($filters);
		$series = [];
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($sentStartDate, $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($sentStartDate, $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($sentStartDate, $filters['endDate']),
			};
			$convertedCatories = [];
			$convertedQueryResult = [];
			$quartersData = [];

			foreach ($queryResult as $data) {
				$convertedQueryResult[$data['date']] = $data;
			}

			$categoriesCount = count($categories);

			for ($i = 0; $i < $categoriesCount; ++$i) {
				$currentDataSplitted = explode(' ', $categories[$i]);
				$currentQ = $currentDataSplitted[0];
				$currentYear = $currentDataSplitted[1] ?? '';
				$previousYear = intval($currentYear) - 1;
				$previousQ = $currentQ;
				$convertedCatories[] = trim("$previousYear $previousQ-$currentYear $currentQ");
				$quartersData['Previous Period'][] = $convertedQueryResult[trim("$previousQ $previousYear")]['total'] ?? 0;
				$quartersData['Current Period'][] = $convertedQueryResult[trim("$currentQ $currentYear")]['total'] ?? 0;
			}
			$result['categories'] = $convertedCatories;

			array_walk($quartersData, function ($data, $quarter) use (&$series) {
				$filteredData = array_filter(array_values($data));

				if (!empty($filteredData)) {
					$series[] = [
						'name' => $quarter,
						'data' => array_values($data),
					];
				}
			});
		}

		$result['series'] = $series;

		return $result;
	}

	public function chartProjectsComparison(array $filters): ?array
	{
		$result = [
			'categories' => [],
		];

		if (!empty($filters['startDate']) && $filters['groupBy']) {
			$rest = '-3 months';
			if ('month' === $filters['groupBy']) {
				$rest = '-1 month';
			} elseif ('year' === $filters['groupBy']) {
				$rest = '-1 year';
			}
			$st = (new \DateTime($filters['startDate']))->modify($rest)->format('Y-m-d H:i:s');
			$filters['startDate'] = $st;
		}

		$queryResult = $this->dashboardRepo->getProjectsHistorical($filters);
		$series = [];
		/** @var AVChart $chart */
		$chart = $filters['chart'];
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$convertedCatories = [];
			$convertedQueryResult = [];
			$quartersData = [];

			foreach ($queryResult as $data) {
				$convertedQueryResult[$data['date']] = $data;
			}
			for ($i = 0; $i < count($categories) - 1; ++$i) {
				$currentDataSplitted = explode(' ', $categories[$i]);
				$nextDataSplitted = explode(' ', $categories[$i + 1]);
				$currentQ = $currentDataSplitted[0];
				$currentYear = $currentDataSplitted[1] ?? '';
				$nextQ = $nextDataSplitted[0];
				$nextYear = $nextDataSplitted[1] ?? '';

				$convertedCatories[] = trim("$currentYear $currentQ - $nextYear $nextQ");
				$quartersData['Previous Period'][] = [
					'total' => $convertedQueryResult[trim("$currentQ $currentYear")]['total'] ?? 0,
					'volume' => $convertedQueryResult[trim("$currentQ $currentYear")]['volume'] ?? 0,
					'date' => "$currentQ $currentYear",
				];
				$quartersData['Current Period'][] = [
					'total' => $convertedQueryResult[trim("$nextQ $nextYear")]['total'] ?? 0,
					'volume' => $convertedQueryResult[trim("$nextQ $nextYear")]['volume'] ?? 0,
					'date' => "$nextQ $nextYear",
				];
			}

			$result['categories'] = $convertedCatories;

			array_walk($quartersData, function ($data, $quarter) use (&$series, $chart) {
				$tempSeries = [];
				foreach ($data as $dat) {
					$tempSeries[] = [
						'name' => $dat['date'],
						'volume' => $this->utilsSrv->amountNumberFormat($dat['volume']),
						'y' => $this->utilsSrv->amountNumberFormat($dat[$chart->getReturnY()]),
					];
				}

				$isEmptyTempSeriesData = $this->isEmptySeriesData($tempSeries);
				if (!$isEmptyTempSeriesData) {
					$series[] = [
						'name' => $quarter,
						'data' => $tempSeries,
					];
				}
			});
		}
		$result['series'] = $series;

		return $result;
	}

	private function chartSpendTmsavingsLanguage(array $filters)
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getTmsavingsPerLanguage($filters);

		foreach ($queryResult as $data) {
			$totalAgreed = $data['totalAgreed'];
			$tmSaving = $data['tmSaving'];
			$percentage = $totalAgreed > 0 ? ($tmSaving * 100) / $totalAgreed : 0;
			$result[] = [
				'name' => $data['language'],
				'y' => floatval($percentage),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'TM savings percentage',
					'data' => $result,
				],
			],
		];
	}

	private function chartSpendTmsavingsComparisonLanguage(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];

		$queryResult = $this->dashboardRepo->getTmsavingsPerLanguage($filters);
		if (count($queryResult)) {
			foreach ($queryResult as $data) {
				$categories[] = $data['language'];
			}
			$result['categories'] = $categories;
			$timeLineData = [
				'TM Saving' => array_fill_keys($categories, null),
				'Total Spent' => array_fill_keys($categories, null),
			];
			foreach ($queryResult as $data) {
				$language = trim($data['language']);
				$timeLineData['Total Spent']["$language"] = $this->utilsSrv->amountNumberFormat($data['totalAgreed']);
				$timeLineData['TM Saving']["$language"] = $this->utilsSrv->amountNumberFormat($data['tmSaving']);
			}
			array_walk($timeLineData, function ($data, $name) use (&$result) {
				$filteredData = array_filter(array_values($data));

				if (!empty($filteredData)) {
					$result['series'][] = [
						'name' => $name,
						'data' => array_values($data),
					];
				}
			});
		}

		return $result;
	}

	private function chartSpendProjectsPhi(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getSpendPhi($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$result['categories'] = $categories;
			$timeLineData = [
				'Total Agreed PHI' => array_fill_keys($categories, 0),
				'Total Agreed non PHI' => array_fill_keys($categories, 0),
			];
			foreach ($queryResult as $data) {
				/** @var \DateTime $date */
				$date = trim($data['date']);

				$timeLineData['Total Agreed PHI']["$date"] += $this->utilsSrv->amountNumberFormat($data['totalPHI']);
				$timeLineData['Total Agreed non PHI']["$date"] += $this->utilsSrv->amountNumberFormat($data['totalAgreed'] - $data['totalPHI']);
			}
			array_walk($timeLineData, function ($data, $name) use (&$result) {
				$filteredData = array_filter(array_values($data));

				if (!empty($filteredData)) {
					$result['series'][] = [
						'name' => $name,
						'data' => array_values($data),
					];
				}
			});
		}

		return $result;
	}

	private function chartSpendProjectsLanguages(array $filters): ?array
	{
		$result = [];
		$queryResult = $this->dashboardRepo->getTmsavingsPerLanguage($filters);

		foreach ($queryResult as $data) {
			$result[] = [
				'name' => $data['language'],
				'y' => $this->utilsSrv->amountNumberFormat($data['totalAgreed']),
			];
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Spend per language',
					'data' => $result,
				],
			],
		];
	}

	public function chartSpendComparisonPrevious(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		if (!empty($filters['startDate']) && !$filters['groupBy']) {
			$rest = '-3 months';
			if ('month' === $filters['groupBy']) {
				$rest = '-1 month';
			} elseif ('year' === $filters['groupBy']) {
				$rest = '-1 year';
			}
			$st = (new \DateTime($filters['startDate']))->modify($rest)->format('Y-m-d H:i:s');
			$filters['startDate'] = $st;
		}
		$queryResult = $this->dashboardRepo->getSpendTmsavings($filters);
		$series = [];
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};

			$convertedCatories = [];
			$convertedQueryResult = [];
			$quartersData = [];

			foreach ($queryResult as $data) {
				$convertedQueryResult[$data['date']] = $data;
			}
			for ($i = 0; $i < count($categories) - 1; ++$i) {
				$currentDataSplitted = explode(' ', $categories[$i]);
				$nextDataSplitted = explode(' ', $categories[$i + 1]);
				$currentQ = $currentDataSplitted[0];
				$currentYear = $currentDataSplitted[1] ?? '';
				$nextQ = $nextDataSplitted[0];
				$nextYear = $nextDataSplitted[1] ?? '';

				$convertedCatories[] = trim("$currentYear $currentQ - $nextYear $nextQ");
				$prevValue = $convertedQueryResult[trim("$currentQ $currentYear")]['totalAgreed'] ?? 0;
				$curValue = $convertedQueryResult[trim("$nextQ $nextYear")]['totalAgreed'] ?? 0;
				if ($prevValue) {
					$prevValue = $this->utilsSrv->amountNumberFormat($prevValue);
				}
				if ($curValue) {
					$curValue = $this->utilsSrv->amountNumberFormat($curValue);
				}
				$quartersData['Previous Period'][] = $prevValue;
				$quartersData['Current Period'][] = $curValue;
			}
			$result['categories'] = $convertedCatories;

			array_walk($quartersData, function ($data, $quarter) use (&$series) {
				if (!empty(array_filter(array_values($data)))) {
					$series[] = [
						'name' => $quarter,
						'data' => array_values($data),
					];
				}
			});
		}
		$result['series'] = $series;

		return $result;
	}

	public function chartSpendProjects(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getProjectsHistorical($filters);
		$series = [];
		$isEmptyData = true;

		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				'month' => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				'quarter' => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				'year' => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
				'volume' => 0,
			]);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"]['total'] += $data['total'];
				$quartersData["$date"]['volume'] += $data['volume'];
				if (0 != $data['total'] || 0 != $data['volume']) {
					$isEmptyData = false;
				}
			}

			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series) {
					if (!count($series)) {
						$series = [
							['name' => 'Total', 'data' => []],
							['name' => 'Volume', 'data' => []],
						];
					}
					$series[0]['data'][] = [
						'name' => "$dateName",
						'y' => $data['total'],
					];
					$series[1]['data'][] = [
						'name' => "$dateName",
						'y' => $this->utilsSrv->amountNumberFormat($data['volume']),
					];
				});
			}
		}

		$result['series'] = $series;

		return $result;
	}

	private function chartProjectsTimeliness(array $filters)
	{
		// PENDING CLARIFY ABOUT SERIES AND GROUP BY
		$queryResult = $this->dashboardRepo->getTimelinesScoreOvertime($filters);
		$processedQueryResult = [
			'Earlier' => ['total' => 0, 'status' => 'Earlier'],
			'OnTime' => ['total' => 0, 'status' => 'OnTime'],
			'Late' => ['total' => 0, 'status' => 'Late'],
		];
		foreach ($queryResult as $dataResult) {
			$currStatus = $this->utilsSrv->getTimelineStatus(trim($dataResult['status']));
			$processedQueryResult[$currStatus]['total'] += $dataResult['total'];
		}
		$totalTasks = $this->dashboardRepo->getTotalTaskByCustomer($filters)['totalTasks'] ?? 0;
		$result = [];
		foreach ($processedQueryResult as $data) {
			if (0 != $data['total'] && 0 != $totalTasks) {
				$result[] = [
					'name' => trim($data['status']),
					'y' => $this->utilsSrv->amountNumberFormat($data['total']),
				];
			}
		}

		$isEmptySeriesData = $this->isEmptySeriesData($result);

		return [
			'series' => $isEmptySeriesData ? [] : [
				[
					'name' => 'Projects',
					'data' => $result,
				],
			],
		];
	}

	private function chartProjectsIssuesTotal(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$series = [];
		$isEmptyData = true;
		$queryResult = $this->dashboardRepo->getProjectsIssues($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'total' => 0,
			]);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"]['total'] += $data['total'];
				if (0 != $data['total']) {
					$isEmptyData = false;
				}
			}

			if (!$isEmptyData) {
				array_walk($quartersData, function ($data, $dateName) use (&$series) {
					$series[] = [
						'name' => $dateName,
						'y' => $data['total'],
					];
				});

				$result['series'][] = [
					'name' => 'Number of issues',
					'data' => $series,
				];
			}
		}

		return $result;
	}

	private function chartProjectsSuccessRate(array $filters)
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$series = [];
		$queryResult = $this->dashboardRepo->getProjectsSuccessRate($filters);
		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = array_fill_keys($categories, [
				'totalNoFeedback' => 0,
				'totalFeedback' => 0,
			]);
			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$quartersData["$date"]['totalNoFeedback'] += $data['totalNoFeedback'];
				$quartersData["$date"]['totalFeedback'] += $data['totalFeedback'];
			}

			array_walk($quartersData, function ($data, $dateName) use (&$series) {
				$percentage = 100;
				if ($data['totalFeedback'] > 0) {
					$percentage = 100 - ($data['totalFeedback'] * 100) / ($data['totalFeedback'] + $data['totalNoFeedback']);
				}
				$series[] = [
					'name' => $dateName,
					'y' => $percentage,
				];
			});
		}

		$isEmptySeriesData = $this->isEmptySeriesData($series);

		if (!$isEmptySeriesData) {
			$result['series'][] = [
				'name' => 'Success rate',
				'data' => $series,
			];
		}

		return $result;
	}

	private function chartCustomerFeedbakAnswer(array $filters): ?array
	{
		$result = [
			'categories' => [],
			'series' => [],
		];
		$queryResult = $this->dashboardRepo->getCustomerFeedbackAnswer($filters);
		$series = [];

		if (count($queryResult)) {
			$categories = match ($filters['groupBy']) {
				self::TIMELINE_MONTH => $this->utilsSrv->getMonthsListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_QUARTER => $this->utilsSrv->getQuartersListInRange($filters['startDate'], $filters['endDate']),
				self::TIMELINE_YEAR => $this->utilsSrv->getYearsListInRange($filters['startDate'], $filters['endDate']),
			};
			$result['categories'] = $categories;
			$quartersData = [];

			foreach ($queryResult as $data) {
				$date = trim($data['date']);
				$question = trim($data['name']);
				if (!isset($quartersData[$question])) {
					$quartersData[$question] = array_fill_keys($categories, [
						'total' => 0,
						'average' => 0,
					]);
				}

				$quartersData[$question]["$date"]['total'] += $data['total'];
				$quartersData[$question]["$date"]['average'] += $data['average'];
			}

			foreach ($quartersData as $key => $quartersDatum) {
				if (!isset($series["$key"])) {
					$series["$key"] = ['name' => "$key", 'data' => []];
				}
				foreach ($quartersDatum as $currDate => $dati) {
					$avg = 0;
					if ($dati['total'] > 0) {
						$avg = $dati['average'] / $dati['total'];
					}

					$series["$key"]['data'][] = [
						'name' => "$currDate",
						'y' => $avg,
					];
				}
			}
		}

		foreach ($series as $serie) {
			$isEmptySeriesData = $this->isEmptySeriesData($serie['data']);
			if (!$isEmptySeriesData) {
				$result['series'][] = $serie;
			}
		}

		return $result;
	}

	private function tableOpiCalls(array $filters)
	{
		$queryResult = $this->dashboardRepo->getOpiCalls($filters);
		$dataResult = [];
		$totalsResult = [
			'duration' => 0,
			'amount' => 0,
		];

		foreach ($queryResult as $data) {
			$language = $data['language'];
			$date = $data['date'];
			$id = $data['id'];
			$requester = $data['requester'] ? $data['requester'] : $data['contact'];
			$duration = $data['duration'];
			$amount = $data['amount'];

			$dataResult[] = [
				'language' => $language,
				'date' => $date->format('Y-m-d'),
				'id' => $id,
				'requester' => $requester,
				'duration' => $duration,
				'amount' => $this->utilsSrv->amountFormat($amount),
			];

			$totalsResult['duration'] += $duration;
			$totalsResult['amount'] += $amount;
		}

		$totalsResult['amount'] = $this->utilsSrv->amountFormat($totalsResult['amount']);

		return [
			'data' => $dataResult,
			'totals' => $totalsResult,
		];
	}

	private function tableInvoicesList(array $filters)
	{
		$queryResult = $this->dashboardRepo->getInvoicesTable($filters);
		$dataResult = [];
		$totalsResult = ['invoiceToDate' => 0, 'contractVolume' => 0, 'contactBalance' => 0];
		$columns = ['Invoice number', 'Invoice date', 'Full paid on', 'Total'];
		foreach ($queryResult as $data) {
			$contractVolume = $data['contractVolume'];
			$total = $data['total'];

			$dataResult[] = [
				'invoiceNumber' => $data['invoiceNumber'],
				'invoiceDate' => $data['invoiceDate']?->format('d/m/Y'),
				'paymentDueDate' => $data['paymentDueDate']?->format('d/m/Y'),
				'total' => $this->utilsSrv->amountFormat($total, 2),
			];

			$totalsResult['invoiceToDate'] += $total;
			$totalsResult['contractVolume'] += $contractVolume;
			$rest = $totalsResult['contractVolume'] - $totalsResult['invoiceToDate'];
			$totalsResult['contactBalance'] = $rest > 0 ? $rest : 0;
		}

		return [
			'data' => $dataResult,
			'columns' => $columns,
			'totals' => $totalsResult,
		];
	}

	private function tableIssuesReported(array $filters)
	{
		$queryResult = $this->dashboardRepo->getIssuesReported($filters);
		$columns = ['Project', 'Office', 'Language', 'Date', 'Description'];

		return [
			'data' => $queryResult,
			'columns' => $columns,
		];
	}
}
