<?php

namespace App\Model\Repository;

use App\Model\Entity\CustomerFeedbackAnswer;
use App\Model\Entity\Task;
use App\Model\Entity\Quote;
use App\Model\Entity\Project;
use App\Model\Entity\Feedback;
use App\Model\Entity\TaskCharge;
use App\Model\Entity\TaskCatCharge;
use App\Model\Entity\CalculationUnit;
use App\Model\Entity\CustomerInvoice;
use App\Model\Entity\BlCall;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

class DashboardRepository
{
	public const TIMELINE_MONTH = 'month';
	public const TIMELINE_QUARTER = 'quarter';
	public const TIMELINE_YEAR = 'year';
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->em = $entityManager;
	}

	public function getProjectsOpen(array $filters): array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro) as total',
			'SUM(pro.totalAgreed) as volume'
		)
			->from(Project::class, 'pro')
			->where($q->expr()->eq('pro.status', ':statusOpen'))
			->setParameters(new ArrayCollection([
				new Parameter('statusOpen', Project::STATUS_OPEN),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getQuotesOpen(array $filters): array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(quote.id)  as total',
			'SUM(quote.totalAgreed) as volume'
		)
			->from(Quote::class, 'quote')
			->where(
				$q->expr()->andX(
					$q->expr()->orX(
						$q->expr()->eq('quote.status', ':statusRequested'),
						$q->expr()->eq('quote.status', ':statusPending'),
						$q->expr()->eq('quote.status', ':statusSent')
					)
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('statusRequested', Quote::STATUS_REQUESTED),
				new Parameter('statusPending', Quote::STATUS_PENDING),
				new Parameter('statusSent', Quote::STATUS_SENT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('quote.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('quote.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getInvoicesUnpaid(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(invoice.id) as total',
			'SUM(invoice.totalNetto) as volume'
		)
			->from(CustomerInvoice::class, 'invoice')
			->where($q->expr()->eq('invoice.paymentState', ':statusUnpaid'))
			->setParameters(new ArrayCollection([
				new Parameter('statusUnpaid', CustomerInvoice::PAYMENT_STATUS_UNPAID),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('invoice.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('invoice.sentDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getSpendTotal(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('SUM(invoice.totalNetto) as total')
			->from(CustomerInvoice::class, 'invoice')
			->where($q->expr()->eq('invoice.state', ':statusSent'))
			->setParameters(new ArrayCollection([
				new Parameter('statusSent', CustomerInvoice::INVOICE_STATUS_SENT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('invoice.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('invoice.finalDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getWordsTotal(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('SUM(tcc.totalQuantity) as total')
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('tcc.calculationUnit', 'unit')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.customerInvoice', 'inv')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('unit.symbol', ':unitSourceWord'),
					$q->expr()->eq('pro.status', ':statusClosed')
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('unitSourceWord', CalculationUnit::UNIT_SOURCE_WORD),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('inv.finalDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getInvoiceAverage(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('AVG(invoice.totalNetto) as total')
			->from(CustomerInvoice::class, 'invoice')
			->where($q->expr()->eq('invoice.state', ':statusSent'))
			->setParameters(new ArrayCollection([
				new Parameter('statusSent', CustomerInvoice::INVOICE_STATUS_SENT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('invoice.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('invoice.finalDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getFeedbackComplaint(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('COUNT(fbr.id) as total')
			->from(Feedback::class, 'fbr')
			->innerJoin('fbr.relatedProject', 'pro')
			->where($q->expr()->eq('fbr.feedbackType', ':typeClientComplain'))
			->setParameters(new ArrayCollection([
				new Parameter('typeClientComplain', Feedback::TYPE_CLIENT_COMPLIANT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getTotalClosedProjects(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('COUNT(pro.id) as total')
			->from(Project::class, 'pro');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getWordsLanguageTaskCatCharge(array $filters): array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(tcc.totalQuantity) as total',
			'langTag.name as langName'
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('tcc.calculationUnit', 'unit')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.targetLanguage', 'langTag')
			->where(
				$q->expr()->andX(
					$q->expr()->orX(
						$q->expr()->eq('unit.symbol', ':unitSourceWord'),
						$q->expr()->eq('unit.symbol', ':unitTargetWord')
					),
					$q->expr()->eq('pro.status', ':statusClosed'),
				)
			)
			->orderBy('total', 'DESC')
			->groupBy('langName')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('unitSourceWord', CalculationUnit::UNIT_SOURCE_WORD),
				new Parameter('unitTargetWord', CalculationUnit::UNIT_TARGET_WORD),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$queryResult = $q->getQuery()->getResult();
		$result = [];
		foreach ($queryResult as $value) {
			$result[$value['langName']] = $value;
		}

		return $result;
	}

	public function getWordsLanguageTaskCharge(array $filters): array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(tc.quantity) as total',
			'langTag.name as langName'
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCharges', 'tc')
			->innerJoin('tc.calculationUnit', 'unit')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.targetLanguage', 'langTag')
			->where(
				$q->expr()->andX(
					$q->expr()->orX(
						$q->expr()->eq('unit.symbol', ':unitSourceWord'),
						$q->expr()->eq('unit.symbol', ':unitTargetWord')
					),
					$q->expr()->eq('pro.status', ':statusClosed')
				)
			)
			->orderBy('total', 'DESC')
			->groupBy('langName')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('unitSourceWord', CalculationUnit::UNIT_SOURCE_WORD),
				new Parameter('unitTargetWord', CalculationUnit::UNIT_TARGET_WORD),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$queryResult = $q->getQuery()->getResult();
		$result = [];
		foreach ($queryResult as $value) {
			$result[$value['langName']] = $value;
		}

		return $result;
	}

	public function getProjectsStatus(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro.id) as total',
			"CASE WHEN pro.status = 'CLAIM' or pro.status = 'CLOSED' THEN 'CLOSED' ELSE pro.status END as status"
		)
			->from(Project::class, 'pro')
			->groupBy('status');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			$q
				->addGroupBy('date')
				->orderBy('date', 'ASC');
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsHistorical(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro.id) as total',
			'SUM(pro.totalAgreed) as volume'
		)
			->from(Project::class, 'pro')
			->where($q->expr()->neq('pro.status', ':statusCanceled'))
			->groupBy('date')
			->orderBy('date', 'ASC')
			->setParameters(new ArrayCollection([
				new Parameter('statusCanceled', Project::STATUS_CANCELLED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['status'])) {
			$q->andWhere(
				$q->expr()->eq('pro.status', ':status')
			)
				->setParameter('status', $filters['status']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getWordsBreakdown(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(COALESCE(tcc.gridNoMatch,0) + COALESCE(tcc.quantityPercent5074,0)) as new_words',
			'SUM(COALESCE(tcc.quantityPercent7584,0) + COALESCE(tcc.quantityPercent8594,0) + COALESCE(tcc.quantityPercent9599,0)) as fuzzy',
			'SUM(COALESCE(tcc.quantityPercent100,0) + COALESCE(tcc.quantityRepetitions,0) + COALESCE(tcc.quantityXTranslated,0)) as leveraged'
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('task.project', 'pro')
			->where($q->expr()->eq('pro.status', ':statusClosed'))
			->orderBy('new_words, fuzzy, leveraged', 'DESC')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getSpendPerServiceTaskCatCharge(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(tcc.totalValue) as total',
			'activityType.name as type',
			"CONCAT(TO_CHAR(pro.closeDate, 'Mon' ), ' ', extract(year from pro.closeDate)) as date"
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('tcc.activityType', 'activityType')
			->innerJoin('task.project', 'pro')
			->where($q->expr()->eq('tcc.status', ':statusSent'))
			->orderBy('date')
			->groupBy('type, date')
			->setParameters(new ArrayCollection([
				new Parameter('statusSent', TaskCatCharge::STATUS_SENT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendPerServiceTaskCharge(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(tc.totalValue) as total',
			'activityType.name as type',
			"CONCAT(TO_CHAR(pro.closeDate, 'Mon' ), ' ', extract(year from pro.closeDate)) as date"
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCharges', 'tc')
			->innerJoin('tc.activityType', 'activityType')
			->innerJoin('task.project', 'pro')
			->where($q->expr()->eq('tc.status', ':statusSent'))
			->orderBy('date')
			->groupBy('type, date')
			->setParameters(new ArrayCollection([
				new Parameter('statusSent', TaskCharge::STATUS_SENT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendHistorical(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('SUM(invoice.totalNetto) as total')
			->from(CustomerInvoice::class, 'invoice')
			->groupBy('date')
			->orderBy('date', 'ASC');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('invoice.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('invoice.finalDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(invoice.finalDate, 'Mon' ), ' ', extract(year from invoice.finalDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from invoice.finalDate), ' ', extract(year from invoice.finalDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(invoice.finalDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getInvoicesOverdue(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			"SUM(CASE WHEN (date_part('day',(CURRENT_TIMESTAMP()-invoice.requiredPaymentDate)) >=30) and ((date_part('day',(CURRENT_TIMESTAMP()-invoice.requiredPaymentDate)))<60) THEN 1 ELSE 0 END) as over_30",
			"SUM(CASE WHEN (date_part('day',(CURRENT_TIMESTAMP()-invoice.requiredPaymentDate)) >=60) and ((date_part('day',(CURRENT_TIMESTAMP()-invoice.requiredPaymentDate)))<90) THEN 1 ELSE 0 END) as over_60",
			"SUM(CASE WHEN (date_part('day',(CURRENT_TIMESTAMP()-invoice.requiredPaymentDate)) >=90) THEN 1 ELSE 0 END) as over_90"
		)
			->from(CustomerInvoice::class, 'invoice')
			->where($q->expr()->eq('invoice.paymentState', ':stateUnpaid'))
			->setParameters(new ArrayCollection([
				new Parameter('stateUnpaid', CustomerInvoice::PAYMENT_STATUS_UNPAID),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('invoice.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getSpendTmsavings(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as totalAgreed',
			'SUM(pro.tmSavings) as tmSaving'
		)
			->from(Project::class, 'pro')
			->groupBy('date')
			->orderBy('date', 'ASC');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getTmsavingsPerLanguage(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as totalAgreed',
			'SUM(pro.tmSavings) as tmSaving',
			'lang.name as language',
		)
			->from(Project::class, 'pro')
			->innerJoin('pro.tasks', 't')
			->innerJoin('t.targetLanguage', 'lang')
			->where($q->expr()->eq('pro.status', ':statusClosed'))
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]))
			->groupBy('language');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getTotalTaskByCustomer(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('COUNT(task.id) as totalTasks')
			->from(Task::class, 'task')
			->innerJoin('task.project', 'pro');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('task.partialDeliveryDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getTimelinesScore(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(task.id) as total',
			'task.ontimeStatus as status'
		)
			->from(Task::class, 'task')
			->innerJoin('task.project', 'pro')
			->groupBy('status');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('task.partialDeliveryDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendCostcenter(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as volume',
			'COUNT(pro.id) as total',
			'pro.costCenter'
		)
			->from(Project::class, 'pro')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('pro.status', ':statusClosed'),
					$q->expr()->isNotNull('pro.costCenter'),
				)
			)
			->orderBy('total', 'DESC')
			->groupBy('pro.costCenter')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendRequester(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as volume',
			'COUNT(pro.id) as total',
			"CONCAT(COALESCE(cp.name,''), ' ' , COALESCE(cp.lastName,'')) as name"
		)
			->from(Project::class, 'pro')
			->innerJoin('pro.customerContactPerson', 'custp')
			->innerJoin('custp.contactPerson', 'cp')
			->where($q->expr()->eq('pro.status', ':statusClosed'))
			->orderBy('total', 'DESC')
			->groupBy('cp.id')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendDepartment(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as volume',
			'COUNT(pro.id) as total',
			'department.id as departmentId',
			'department.name as departmentName'
		)
			->from(Project::class, 'pro')
			->innerJoin('pro.customerContactPerson', 'custp')
			->innerJoin('custp.contactPerson', 'cp')
			->innerJoin('cp.personDepartment', 'department')
			->where($q->expr()->eq('pro.status', ':statusClosed'))
			->orderBy('total', 'DESC')
			->groupBy('department.id')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsRush(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro.id) as total',
			'SUM(pro.totalAgreed) as volume'
		)
			->from(Project::class, 'pro')
			->where($q->expr()->eq('pro.rush', ':rush'))
			->groupBy('date')
			->orderBy('date', 'ASC')
			->setParameters(new ArrayCollection([
				new Parameter('rush', true),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getTasksRushLanguage(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(task.id) as total',
			'SUM(task.totalAgreed) as volume',
			'lang.name as language',
		)
			->from(Task::class, 'task')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.targetLanguage', 'lang')
			->where($q->expr()->eq('pro.rush', ':rush'))
			->groupBy('language')
			->setParameters(new ArrayCollection([
				new Parameter('rush', true),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('task.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsMinimum(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro.id) as total',
			'SUM(pro.totalAgreed) as volume'
		)
			->from(Project::class, 'pro')
			->where($q->expr()->eq('pro.minimum', ':minimum'))
			->setParameters(new ArrayCollection([
				new Parameter('minimum', true),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date")
						->groupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date")
						->groupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date")
						->groupBy('date')
						->orderBy('date', 'ASC');
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsMinimumLanguages(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(task.id) as total',
			'SUM(task.totalAgreed) as volume',
			'lang.name as language',
		)
			->from(Task::class, 'task')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.targetLanguage', 'lang')
			->groupBy('language')
			->where($q->expr()->eq('pro.minimum', ':minimum'))
			->setParameters(new ArrayCollection([
				new Parameter('minimum', true),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendPerServiceHours(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(task.id) as tasks',
			'SUM(tc.quantity) as hours',
			'SUM(tc.totalValue) as cost',
			'lang.name as language',
			'activityType.name as activity'
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCharges', 'tc')
			->innerJoin('tc.activityType', 'activityType')
			->innerJoin('task.project', 'pro')
			->innerJoin('tc.calculationUnit', 'cu')
			->innerJoin('task.targetLanguage', 'lang')
			->where($q->expr()->eq('cu.id', ':idCalculation'))
			->groupBy('lang, activity')
			->setParameters(new ArrayCollection([
				new Parameter('idCalculation', 7),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsGenre(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(pro.id) as total',
			'SUM(pro.totalAgreed) as volume',
			'pro.genre as genre'
		)
			->from(Project::class, 'pro')
			->groupBy('genre');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getWordsHistorical(array $filters): array
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(tcc.totalQuantity) as total'
		)
			->from(Task::class, 'task')
			->innerJoin('task.projectPartFinance', 'taskFinance')
			->innerJoin('taskFinance.taskCatCharges', 'tcc')
			->innerJoin('tcc.calculationUnit', 'unit')
			->innerJoin('task.project', 'pro')
			->innerJoin('task.targetLanguage', 'langTag')
			->where(
				$q->expr()->andX(
					$q->expr()->orX(
						$q->expr()->eq('unit.symbol', ':unitSourceWord'),
						$q->expr()->eq('unit.symbol', ':unitTargetWord')
					),
					$q->expr()->eq('pro.status', ':statusClosed'),
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('unitSourceWord', CalculationUnit::UNIT_SOURCE_WORD),
				new Parameter('unitTargetWord', CalculationUnit::UNIT_TARGET_WORD),
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.closeDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date")
						->addGroupBy('date')
						->orderBy('date', 'ASC');
					break;
				default:
					return [];
			}
		}

		$queryResult = $q->getQuery()->getResult();
		$result = [];
		foreach ($queryResult as $value) {
			$result[$value['date']] = $value;
		}

		return $result;
	}

	public function getOpiCalls(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'c.blReferenceId as id',
			'c.startDate as date',
			'lang.englishName as language',
			'c.requester as requester',
			'cont.name as contact',
			'c.customerDuration as duration',
			'c.customerAmount as amount',
		)
			->from(BlCall::class, 'c')
			->innerJoin('c.blTargetLanguage', 'lang')
			->innerJoin('c.blContact', 'cont')
			->innerJoin('c.blCustomer', 'blcust')
			->innerJoin('blcust.customer', 'cust')
			->orderBy('c.id', 'DESC');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('cust.id', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('c.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getResult();
	}

	public function getSpendPhi(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(pro.totalAgreed) as totalAgreed',
			"CASE WHEN cat.id = '29' THEN SUM(pro.totalAgreed) ELSE SUM(0) END as totalPHI"
		)
			->from(Project::class, 'pro')
			->innerJoin('pro.categories', 'cat')
			->groupBy('date, cat.id')
			->orderBy('date', 'ASC');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}
		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getTimelinesScoreOvertime(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'COUNT(task.id) as total',
			'task.ontimeStatus as status'
		)
			->from(Task::class, 'task')
			->innerJoin('task.project', 'pro')
			->groupBy('date,status');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('task.partialDeliveryDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(task.partialDeliveryDate, 'Mon' ), ' ', extract(year from task.partialDeliveryDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from task.partialDeliveryDate), ' ', extract(year from task.partialDeliveryDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(task.partialDeliveryDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getProjectsIssues(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('COUNT(fb.id) as total')
			->from(Feedback::class, 'fb')
			->innerJoin('fb.relatedProject', 'pro')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('pro.status', ':statusClosed'),
					$q->expr()->in('fb.feedbackType', ':fbTypes')
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('fbTypes', [Feedback::TYPE_CLIENT_COMPLIANT, Feedback::TYPE_INTERNAL_NONCONFORMITY]),
			]))
			->groupBy('date');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('fb.creationDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(fb.creationDate, 'Mon' ), ' ', extract(year from fb.creationDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from fb.creationDate), ' ', extract(year from fb.creationDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(fb.creationDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getTotalIssues(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select('COUNT(pro) as total')
			->from(Feedback::class, 'fb')
			->innerJoin('fb.relatedProject', 'pro')
			->where(
				$q->expr()->andX(
					$q->expr()->eq('pro.status', ':statusClosed'),
					$q->expr()->in('fb.feedbackType', ':fbsTypes'),
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('fbsTypes', [Feedback::TYPE_CLIENT_COMPLIANT, Feedback::TYPE_INTERNAL_NONCONFORMITY]),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('fb.creationDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		$result = $q->getQuery()->getResult();
		if (is_array($result)) {
			$result = array_shift($result);
		}

		return $result;
	}

	public function getProjectsSuccessRate(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'SUM(CASE WHEN fb.id IS NULL THEN 1 ELSE 0 END) as totalNoFeedback',
			'SUM(CASE WHEN fb.id IS NOT NULL and fb.feedbackType IN(:fbTypes) THEN 1 ELSE 0 END) as totalFeedback'
		)
			->from(Project::class, 'pro')
			->leftJoin('pro.feedbacks', 'fb')
			->where(
				$q->expr()->orX(
					$q->expr()->eq('pro.status', ':statusClosed'),
					$q->expr()->eq('pro.status', ':statusClaim'),
				)
			)
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
				new Parameter('statusClaim', Project::STATUS_COMPLAINT),
				new Parameter('fbTypes', [Feedback::TYPE_CLIENT_COMPLIANT, Feedback::TYPE_INTERNAL_NONCONFORMITY]),
			]))
			->groupBy('date');

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getCustomerFeedbackAnswer(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'cfbq.id',
			'cfbq.name',
			'COUNT(cfba.id) as total',
			'SUM(CAST(cfba.value AS int)) as average',
		)
			->from(CustomerFeedbackAnswer::class, 'cfba')
			->innerJoin('cfba.customerFeedbackQuestion', 'cfbq')
			->innerJoin('cfba.project', 'pro')
			->where($q->expr()->eq('pro.status', ':statusClosed'))
			->groupBy('cfbq.id, date')
			->orderBy('date', 'ASC')
			->setParameters(new ArrayCollection([
				new Parameter('statusClosed', Project::STATUS_CLOSED),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('pro.startDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		if (isset($filters['groupBy'])) {
			switch ($filters['groupBy']) {
				case self::TIMELINE_MONTH:
					$q->addSelect("CONCAT(TO_CHAR(pro.startDate, 'Mon' ), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_QUARTER:
					$q->addSelect("CONCAT('Q' , extract(quarter from pro.startDate), ' ', extract(year from pro.startDate)) as date");
					break;
				case self::TIMELINE_YEAR:
					$q->addSelect("TO_CHAR(pro.startDate, 'YYYY' ) as date");
					break;
				default:
					return [];
			}
		}

		return $q->getQuery()->getResult();
	}

	public function getInvoicesTable(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'invoice.finalNumber as invoiceNumber',
			'invoice.finalDate as invoiceDate',
			'invoice.requiredPaymentDate as paymentDueDate',
			'invoice.totalNetto as total',
			'cust.contractVolume',
		)
			->from(CustomerInvoice::class, 'invoice')
			->innerJoin('invoice.customer', 'cust')
			->orderBy('invoiceDate', 'DESC')
			->andWhere($q->expr()->eq('invoice.state', ':state'))
			->setParameter('state', CustomerInvoice::INVOICE_STATUS_SENT);

		if (isset($filters['customerId'])) {
			$q
				->andWhere($q->expr()->eq('invoice.customer', ':customerId'))
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['status'])) {
			$q
				->andWhere($q->expr()->eq('invoice.paymentState', ':status'))
				->setParameter('status', $filters['status']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere($q->expr()->between('invoice.finalDate', ':startDate', ':endDate'))
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getArrayResult();
	}

	public function getIssuesReported(array $filters): mixed
	{
		$q = $this->em->createQueryBuilder();
		$q->select(
			'pro.idNumber as projectNumber',
			'cust.fullName as customerName',
			'lang.name as languages'
		)
			->addSelect("TO_CHAR(fbr.creationDate, 'MM-DD-YYYY' ) as creationDate")
			->addSelect('fbr.descriptionOfClaim as description')
			->from(Feedback::class, 'fbr')
			->innerJoin('fbr.relatedProject', 'pro')
			->innerJoin('pro.customer', 'cust')
			->leftJoin('fbr.tasks', 't')
			->leftJoin('t.targetLanguage', 'lang')
			->groupBy('projectNumber, customerName, creationDate, languages, description ')
			->where($q->expr()->eq('fbr.feedbackType', ':typeClientComplain'))
			->setParameters(new ArrayCollection([
				new Parameter('typeClientComplain', Feedback::TYPE_CLIENT_COMPLIANT),
			]));

		if (isset($filters['customerId'])) {
			$q->andWhere(
				$q->expr()->eq('pro.customer', ':customerId')
			)
				->setParameter('customerId', $filters['customerId']);
		}

		if (isset($filters['startDate']) && isset($filters['endDate'])) {
			$q
				->andWhere(
					$q->expr()->between('fbr.creationDate', ':startDate', ':endDate')
				)
				->setParameter('startDate', $filters['startDate'])
				->setParameter('endDate', $filters['endDate']);
		}

		return $q->getQuery()->getArrayResult();
	}
}
