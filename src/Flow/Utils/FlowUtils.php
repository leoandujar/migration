<?php

namespace App\Flow\Utils;

use App\Service\UtilService;
use App\Service\Xtrf\XtrfQuoteService;
use App\Command\Services\Helper;

class FlowUtils
{
	public const TYPE_PROJECT = 'project';
	public const TYPE_QUOTE = 'quote';
	public const DEADLINE_FORMAT_DATETIME = 1;
	public const DEADLINE_FORMAT_TIMESTAMP = 2;
	public const DEADLINE_FORMAT_STRING = 3;

	private XtrfQuoteService $xtrfQuoteSrv;
	private UtilService $utilsSrv;

	public function __construct(
		XtrfQuoteService $xtrfQuoteSrv,
		UtilService $utilsSrv
	) {
		$this->xtrfQuoteSrv = $xtrfQuoteSrv;
		$this->utilsSrv = $utilsSrv;
	}

	public function getFullTemplate($template, $type, $deadline, $name_options = null, $name_prefix = null): array
	{
		$name = $this->defineNameCopy($name_prefix, $name_options);
		switch ($type) {
			case self::TYPE_PROJECT:
				if (isset($template['contact_person'])) {
					$contact_person = $template['contact_person'];
					unset($template['contact_person']);
				}

				if (isset($template['project_manager'])) {
					$projectManager = $template['project_manager'];
					unset($template['project_manager']);
					$template['people'] = [
						'responsiblePersons' => [
							'projectManagerId' => $projectManager,
						],
					];

					if (isset($template['project_coordinator'])) {
						$projectCoordinator = $template['project_coordinator'];
						unset($template['project_coordinator']);
						$template['people']['responsiblePersons']['projectCoordinatorId'] = $projectCoordinator;
					}
				}

                $template['sourceLanguageId'] ??= $template['source_language'] ?? [];
                $template['targetLanguagesIds'] ??= $template['target_languages'] ?? [];
                $template['specializationId'] ??= $template['specialization'] ?? [];
                $template['serviceId'] ??= $template['service'] ?? [];
                $template['categoriesIds'] ??= $template['categories'] ?? [];
                $template['customerId'] ??= $template['customer_id'] ?? null;
                $template['instructions'] ??= $template['instructions'] ?? null;
                $template['name'] = $name;
                $template['contact_person'] = $contact_person ?? 0;
                unset($template['source_language']);
                unset($template['target_languages']);
                unset($template['specialization']);
                unset($template['service']);
                unset($template['categories']);
                $template['dates'] = [
                    'startDate' => ['time' => (new \DateTime())->getTimestamp() * 1000],
                    'deadline' => ['time' => $this->getDeadlineCopy($deadline, self::DEADLINE_FORMAT_TIMESTAMP)],
                ];
                $template['inputFiles'] = [];

                return [
                    'template' => $template,
                    'specificData' => [
                        'contact_person' => $contact_person ?? 0,
                    ],
                ];

			case self::TYPE_QUOTE:
				$template['name'] = $name;
				$template['custom_fields'] = $template['custom_fields'] ?? [];
				$template['deliveryDate'] = $this->getDeadlineCopy($deadline, self::DEADLINE_FORMAT_STRING);
				$instructions = $template['instructions'] ?? null;
				unset($template['instructions']);
				$sessionID = $this->xtrfQuoteSrv->xtrfLoginWithToken($template['contact_person']);

				return [
					'template' => $template,
					'specificData' => [
						'sessionID' => $sessionID,
						'instructions' => $instructions,
					],
				];
		}

		return [];
	}

    public static function orderTemplateTest(array $template, string $deadline): array
    {
        if (isset($template['project_manager'])) {
            $projectManager = $template['project_manager'];
            unset($template['project_manager']);
            $template['people'] = [
                'responsiblePersons' => [
                    'projectManagerId' => $projectManager,
                ],
            ];

            if (isset($template['project_coordinator'])) {
                $projectCoordinator = $template['project_coordinator'];
                unset($template['project_coordinator']);
                $template['people']['responsiblePersons']['projectCoordinatorId'] = $projectCoordinator;
            }
        }

        $template['customerId'] = $template['customer_id'] ?? null;
        $template['sourceLanguageId'] = $template['source_language'] ?? null;
        $template['specializationId'] = $template['specialization'] ?? null;
        $template['serviceId'] = $template['service'] ?? null;
        $template['categoriesIds'] = $template['categories'] ?? [];
        $template['instructions'] = $template['instructions'] ?? null;
        unset($template['customer_id'], $template['source_language'],$template['target_languages'],$template['specialization'],$template['service'],$template['categories']);
        $template['dates'] = [
            'startDate' => ['time' => (new \DateTime())->getTimestamp() * 1000],
            'deadline' => ['time' => self::getDeadlineCopy($deadline, self::DEADLINE_FORMAT_TIMESTAMP)],
        ];

        return $template;
    }

	public static function defineNameCopy(?string $name_prefix = null, ?string $name_option = null): string
	{
		$name = $name_prefix ?? 'testName';
		if (null == $name_option) {
			$name .= match ($name_option) {
				'DATE' => sprintf(' - %s', (new \DateTime())->format('D M j Y')),
			};
		}

		return $name;
	}

	public static function getDeadlineCopy($deadline, int $returnType = self::DEADLINE_FORMAT_DATETIME, string $format = 'Y-m-d H:i:s'): \DateTime|int|string
	{
		$deadlineTime = null;
		if (is_numeric($deadline)) {
			$deadline = sprintf('%dD', $deadline);
		}

		$deadline = Helper::deadline($deadline, $deadlineTime);

		return match ($returnType) {
			self::DEADLINE_FORMAT_TIMESTAMP => $deadline->getTimestamp() * 1000,
			self::DEADLINE_FORMAT_STRING => $deadline->format($format),
			default => $deadline,
		};
	}

	public function prepareCollectInvoicesQbo(array &$filters): bool
    {
		if (!self::validCollectInvoicesQbo($filters)) {
			return false;
		}
		if (!empty($filters['final_date']) && !is_array($filters['final_date'])) {
			$filters['final_date'] = [
				$this->utilsSrv->getDateByFormat($filters['final_date'])->format('d/m/Y'),
				(new \DateTime('now'))->format('d/m/Y'),
			];
		}
		if (!empty($filters['sent_date']) && !is_array($filters['sent_date'])) {
			$filters['sent_date'] = [
				$this->utilsSrv->getDateByFormat($filters['sent_date'])->format('Y/m/d'),
				(new \DateTime('now'))->format('Y/m/d'),
			];
		}
		$filters['not_synced'] =  true;

		return true;
	}

	private static function validCollectInvoicesQbo(array $filters): bool
	{
		$mandatoryFilters = $filters['search'] ? ['search'] : ['sent_date', 'status'];
		$diff = array_diff(array_keys($filters), $mandatoryFilters);
		if (count($diff)) {
			return false;
		}

		return true;
	}

	public function prepareCollectBlCalls(array &$filters): bool
    {
		if (!self::validCollectBlCalls($filters)) {
			return false;
		}
		if (!is_array($filters['start_date'])) {
			$filters['start_date'] = [
				$this->utilsSrv->getDateByFormat($filters['start_date'])->format('d/m/Y'),
				(new \DateTime('now'))->format('d/m/Y'),
			];
		}

		return true;
	}

	private static function validCollectBlCalls(array $filters): bool
	{
		$mandatoryFilters = ['customer_id', 'start_date'];
		$diff = array_diff(array_keys($filters), $mandatoryFilters);
		if (count($diff)) {
			return false;
		}

		return true;
	}

	public static function buildDeadLine(string $sla): int
	{
		$now = new \DateTime('now', new \DateTimeZone('America/Los_Angeles'));
		$time = $now->format('H');
		$now->setTime(18, 0);
		if (preg_match('/(\d+)\s*hour/i', $sla, $matches)) {
			$hours = intval($matches[1]);
			$now->modify("+$hours hours");
		} elseif ($time >= 17) {
			$now->modify('+1 day');
		}

		return $now->getTimestamp() * 1000;
	}
}
