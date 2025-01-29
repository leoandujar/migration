<?php

namespace App\MessageHandler;

use App\Command\Services\Helper;
use App\Message\XtmLqaCategoriesCsvMessage;
use App\Model\Entity\LqaIssueType;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class XtmLqaCategoriesCsvMessageHandler
{
	private LoggerService $loggerSrv;
	private EntityManagerInterface $em;
    private ParameterBagInterface $bag;
	private string $filePath = '/src/Command/Data/lqa-issue-types.json';

	public function __construct(
		LoggerService $loggerSrv,
		EntityManagerInterface $em,
        ParameterBagInterface $bag
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
        $this->bag = $bag;
	}

	public function __invoke(XtmLqaCategoriesCsvMessage $message): void
	{
		$this->loggerSrv->addInfo('Initializing BRANCHES');

		$sourceJson = file_get_contents("{$this->bag->get('kernel.project_dir')}$this->filePath");
		$sourceData = json_decode($sourceJson, true);
		if (is_array($sourceData)) {
			$this->updateEntries($sourceData);
		}
	}

	/**
	 * @param int $level
	 */
	public function updateEntries(array $elements, ?LqaIssueType $parent = null, $level = 0): mixed
	{
		foreach ($elements as $element) {
			$entity = $this->em->getRepository(LqaIssueType::class)->findOneByNameAndParent($element['name'], $parent);
			$executedOperation = Helper::UPDATED;
			if (null === $entity) {
				$entity = new LqaIssueType();
				$entity->setName($element['name']);
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

			$remoteSource = [
				'weight' => 1,
				'isLeaf' => !isset($element['childs']),
				'parent' => $parent,
				'active' => true,
				'path' => $path,
				'pathDepth' => $pathDepth,
			];

			$entityHash = $entity->hashFromObject();
			$remoteHash = $entity->hashFromRemote($remoteSource);

			if ($entityHash === $remoteHash) {
				$executedOperation = Helper::NOT_CHANGED;
			} else {
				$entity->populateFromRemote($remoteSource);
			}
			try {
				$this->em->persist($entity);
			} catch (\Throwable $thr) {
				$message = "Error while flushing LqaIssueType=>{$entity->getName()}";
				$this->loggerSrv->addError($message, $thr);
				$this->loggerSrv->addInfo($message);
				continue;
			}

			$outputText = match ($executedOperation) {
				Helper::CREATED => "LqaIssueType Level $level => {$element['name']}: created",
				Helper::UPDATED => "LqaIssueType Level $level => {$element['name']}: updated",
				Helper::NOT_CHANGED => "LqaIssueType Level $level => {$element['name']}: up to date",
				default => '',
			};
			$this->loggerSrv->addInfo($outputText);

			if (isset($element['childs'])) {
				$this->updateEntries($element['childs'], $entity, ++$level);
			}
		}
		$this->em->flush();

		return null;
	}
}
