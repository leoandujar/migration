<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Message\XtmLqaCategoriesJsonMessage;
use App\Model\Entity\LqaIssueType;
use App\Model\Entity\LqaIssueTypeMapping;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmLqaCategoriesJsonMessageHandler
{
	private EntityManagerInterface $em;
	private ParameterBagInterface $bag;
	private string $filePath = '/src/Command/Data/lqa-xtm-all.csv';
	private LoggerService $loggerSrv;
	private array $entities = [];

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
		ParameterBagInterface $bag,
	) {
		$this->em = $em;
		$this->bag = $bag;
		$this->loggerSrv = $loggerSrv;
	}

	public function __invoke(XtmLqaCategoriesJsonMessage $message): void
	{
		$this->loggerSrv->addInfo('Initializating branches');
		$fp = fopen("{$this->bag->get('kernel.project_dir')}$this->filePath", 'r');
		$delimiter = ',';
		$headers = fgetcsv($fp, 0, $delimiter);
		// Remove BOM
		$headers[0] = str_replace("\xEF\xBB\xBF", '', $headers[0]);

		while ($line = fgetcsv($fp, 0, $delimiter)) {
			if (empty($line[0])) {
				continue;
			}

			$row = [];
			foreach ($headers as $key => $name) {
				if (isset($line[$key])) {
					$row[$name] = trim($line[$key]);
				}
				$row['active'] = true;
			}

			try {
				$executedOperation = $this->updateEntry($row);
			} catch (\Throwable $thr) {
				$message = "Error while procesing row {$row['IssueType']}";
				$this->loggerSrv->addError($message, $thr);
				$this->loggerSrv->addInfo("$message");
				continue;
			}

			$level = $row['LevelDepth'] - 1;
			$outputText = match ($executedOperation) {
				Helper::CREATED => "LQA Issue Type Level $level => {$row['IssueType']}: created",
				Helper::IGNORED => "LQA Issue Type Level $level => {$row['IssueType']}: Ignored due Parent was not processed yet.",
				Helper::UPDATED => "LQA Issue Type Level $level => {$row['IssueType']}: updated",
				Helper::NOT_CHANGED => "LQA Issue Type Level $level => {$row['IssueType']}: up to date",
				default => '',
			};
			$this->loggerSrv->addInfo("$outputText");
		}
	}

	public function updateEntry(array $element): int
	{
		$parent = null;
		if (!empty($element['ParentID'])) {
			if (isset($this->entities[$element['ParentID']]) && $this->entities[$element['ParentID']] instanceof LqaIssueTypeMapping) {
				$parent = $this->entities[$element['ParentID']];
			} else {
				return Helper::IGNORED;
			}
		} elseif (isset($element['Parent']) && $element['Parent'] instanceof LqaIssueTypeMapping) {
			$parent = $element['Parent'];
		}

		$executedOperation = Helper::UPDATED;
		$entity = $this->em->getRepository(LqaIssueTypeMapping::class)->findOneByNameAndParent($element['IssueType'], $parent);
		if (null === $entity) {
			$entity = new LqaIssueTypeMapping();
			$entity->setName($element['IssueType']);
			$executedOperation = Helper::CREATED;
		}

		if (null === $parent) {
			$path = null;
			$pathDepth = 0;
		} else {
			if (null === $parent->getPath()) {
				$path = $parent->getId();
			} else {
				$path = $parent->getPath().'|'.$parent->getId();
			}
			$pathDepth = $parent->getPathDepth() + 1;
		}

		$issueType = $this->em->getRepository(LqaIssueType::class)->findOneByName($this->normalizeName($element['IssueType']));

		$remoteSource = [
			'weight' => 1,
			'parent' => $parent,
			'active' => $element['active'],
			'lqaIssueType' => $issueType,
			'path' => $path,
			'pathDepth' => $pathDepth,
		];

		$entityHash = $entity->hashFromObject();
		$remoteHash = $entity->hashFromRemote($remoteSource);

		if ($entityHash === $remoteHash) {
			$executedOperation = Helper::NOT_CHANGED;
		} else {
			$entity->populateFromRemote($remoteSource);

			$this->em->persist($entity);
			$this->em->flush();
		}

		if (!empty($element['ID'])) {
			$this->entities[$element['ID']] = $entity;
		}

		return $executedOperation;
	}

	public function normalizeName($name): string
	{
		return trim(strtr(preg_replace('/\.$/', '', $name), [
			'/ ' => '/',
			' /' => '/',
			' / ' => '/',
		]));
	}
}
