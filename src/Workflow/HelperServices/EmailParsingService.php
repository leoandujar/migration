<?php

namespace App\Workflow\HelperServices;

use App\Apis\Shared\Util\UtilsService;
use App\Service\LoggerService;
use App\Service\Notification\NotificationService;
use App\Service\Notification\TeamNotification;
use App\Service\RegexService;
use Doctrine\ORM\EntityManagerInterface;
use Postmark\Inbound;

class EmailParsingService
{
	private const ENTITY_OPERATION_EQ = 'eq';
	private const ENTITY_OPERATION_LIKE = 'like';

	private LoggerService $loggerSrv;
	private UtilsService $utilsSrv;
	private EntityManagerInterface $em;

	private array $mappings = [];
	private array $dataProcessed = [];
	private NotificationService $notificationSrv;

	public function __construct(
		LoggerService $loggerSrv,
		UtilsService $utilsSrv,
		EntityManagerInterface $em,
		NotificationService $notificationSrv,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->em = $em;
		$this->utilsSrv = $utilsSrv;
		$this->notificationSrv = $notificationSrv;
	}

	public function initProcess(&$params, &$files, $data, $globalPrefix, $inlineLabel = false, bool $isFromEmail = true)
	{
		$text = $this->cleanText($data['TextBody']);
		$this->dataProcessed = $this->prepareText($text, $globalPrefix, $inlineLabel);

		array_walk_recursive($params, function (&$value, $key) {
			if (null === $value && isset($this->mappings['entities'][$key])) {
				$map = $this->mappings['entities'][$key] ?? [];
				$this->processValue($value, $map);
			}
		});

		if (isset($params['custom_fields'])) {
			foreach ($params['custom_fields'] as &$customField) {
				if (null === $customField['value'] && isset($this->mappings['entities']['custom_fields'][$customField['key']])) {
					$map = $this->mappings['entities']['custom_fields'][$customField['key']] ?? [];
					$this->processValue($customField['value'], $map);
				}
			}
		}

		// ATTACHMENTS
		$inbound = new Inbound(json_encode($data));
		$attachmentList = $inbound->Attachments() ?? [];
		$hasFiles = false;
		foreach ($attachmentList as $attachment) {
			$hasFiles = true;
			$files[] = [
				'filename' => $attachment->Name,
				'basename' => $attachment->Name,
				'content' => base64_encode(base64_decode(chunk_split($attachment->Content))),
			];
		}
		if (!$hasFiles && $isFromEmail) {
			$data = [
				'title' => 'No attachment',
				'message' => 'Email parsing was called without attachment.',
				'status' => TeamNotification::STATUS_FAILURE,
				'date' => (new \DateTime())->format('Y-m-d'),
			];
			$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, null, $data);
			$this->loggerSrv->addWarning('Email parsing was calling without attachments.', $params);
		}
	}

	public function initMappings($mappings)
	{
		$this->mappings = $mappings;
	}

	/**
	 * @return null
	 */
	public function processValue(&$value, $map)
	{
		$type = $map['type'] ?? null;
		$label = $map['label'] ?? null;
		$patternType = $map['pattern_type'] ?? null;
		$pattern = $map['pattern'] ?? null;
		$entity = $map['entity'] ?? null;
		$field = $map['field'] ?? null;
		$condition = $map['condition'] ?? [];
		$entityOperation = $map['operation'] ?? self::ENTITY_OPERATION_EQ;
		$default = $map['default'] ?? null;
		$format = $map['format'] ?? null;
		$suffix = (isset($map['suffix']) || ctype_space($map['suffix'])) ? $map['suffix'] : null;

		if ($entity && $field && $label) {
			try {
				$valueFromMapping = $this->getValueFromData($label, $type, $suffix, $patternType, $pattern, $default);
				if (is_array($valueFromMapping) && 'array' === $type) {
					foreach ($valueFromMapping as $item) {
						$item = trim($item);
						$object = $this->getEntityOrDefault($entity, $field, $item, $default, $entityOperation, $condition);
						if (!$object) {
							$this->loggerSrv->addWarning("Label $label: Object could not be found in EmailParsing workflow. Skipping for now");
							continue;
						}

						if (is_string($object)) {
							$value[] = $object;
							continue;
						}
						$value[] = $object->getId();
					}
				} else {
					$object = $this->getEntityOrDefault($entity, $field, $valueFromMapping, $default, $entityOperation, $condition);
					if (!$object) {
						$this->loggerSrv->addWarning("Field $field: Object could not be found in EmailParsing workflow. Skipping for now");

						return;
					}
					if (is_string($object)) {
						$value = $object;
					} elseif ('string' === $type) {
						$value = $object->getId();
					} elseif ('array' === $type) {
						$value[] = $object->getId();
					}
				}
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError("Unable to get object for EmailParsing workflow for entity=>$entity field=>$field and value=>$value", $thr);
			}
		} else {
			$valueFromMapping = $this->getValueFromData($label, $type, $suffix, $patternType, $pattern, $default);
			if (empty($format)) {
				$value = $valueFromMapping;

				return;
			}
			$value = $this->getDeadline($valueFromMapping, $format);
		}
	}

	private function getEntityOrDefault(string $entity, string $field, string $value, $default, $entityOperation = self::ENTITY_OPERATION_EQ, array $condition = [])
	{
		if (self::ENTITY_OPERATION_EQ === $entityOperation) {
			$paramsValue = [$field => strtolower($value)];
			if ($condition) {
				$paramsValue = array_merge($paramsValue, $condition);
			}
			$object = $this->em->getRepository("App\\Model\\Entity\\$entity")->findOneBy($paramsValue);
		}
		if (self::ENTITY_OPERATION_LIKE === $entityOperation) {
			$object = $this->em->getRepository("App\\Model\\Entity\\$entity")->findByMapping($value);
		}
		if ($object) {
			return $object;
		}
		if (!empty($default)) {
			return $default;
		}

		return null;
	}

	private function getValueFromData($label, $type, $suffix, $patternType, $pattern, $default)
	{
		$result = null;
		if ($patternType) {
			$matches = [];
			if (!empty($this->dataProcessed[$label])) {
				RegexService::match($patternType, $this->dataProcessed[$label], $pattern, $matches);
			}
			$value = $matches[0] ?? null;

			if ($value) {
				$this->dataProcessed[$label] = trim(str_replace($value, '', $this->dataProcessed[$label]));
				$result = trim($value);
			}
		}

		if (!$result && isset($this->dataProcessed[$label]) && !$suffix && !$patternType && !$pattern) {
			return $this->dataProcessed[$label];
		}

		if (!$result && isset($this->dataProcessed[$label]) && $suffix && !$patternType && !$pattern) {
			$suffix = $this->getSuffix($suffix, $this->dataProcessed[$label]);
			if (!$suffix) {
				$result = $this->dataProcessed[$label];
			}
			if (!$result && 'array' === $type) {
				$result = explode($suffix, $this->dataProcessed[$label]);
			}

			if (!$result && 'string' === $type) {
				$explode = explode($suffix, $this->dataProcessed[$label]);
				if (count($explode)) {
					$this->dataProcessed[$label] = trim(str_replace("$explode[0]$suffix", '', $this->dataProcessed[$label]));
					$result = $explode[0];
				}
			}
		}

		if (null === $result) {
			$result = $default;
		}

		return $result;
	}

	private function getSuffix($suffix, $valueToCheck)
	{
		if (empty($suffix)) {
			return null;
		}

		if (!is_array($suffix)) {
			return $suffix;
		}

		foreach ($suffix as $suff) {
			if (empty($suff)) {
				continue;
			}
			if (count(explode($suff, $valueToCheck)) > 1) {
				return $suff;
			}
		}

		return null;
	}

	public function cleanText($text)
	{
		return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $text);
	}

	/**
	 * @return array
	 */
	public function prepareText($text, $globalPrefix, bool $inlineLabel = false)
	{
		$result = [];
		$data = explode(PHP_EOL, $text);
		$key = '';
		foreach ($data as $item) {
			$value = '';
			$item = trim($item);

			if (!preg_match('/[a-zA-Z\d]/i', $item)) {
				continue;
			}

			if ($inlineLabel) {
				$splitData = explode($globalPrefix, $item, 2);
				if (2 === count($splitData)) {
					$result[$splitData[0]] = "$splitData[1]";
					continue;
				} elseif (count($splitData) > 2) {
					$currentKey = '';
					foreach ($splitData as $current) {
						if (empty($currentKey)) {
							$currentKey = $current;
							continue;
						}
						$result[$currentKey] = $current;
						$currentKey = null;
					}
				}
			}
			if ($this->utilsSrv->stringEndsWith($item, $globalPrefix) || $this->utilsSrv->stringEndsWith($item, "$globalPrefix\r")) {
				$key = trim($this->utilsSrv->removeSubstringFromEnd($globalPrefix, $item));
			} else {
				$value = trim($item);
			}

			if (!isset($result[$key])) {
				$result[$key] = '';
			}

			if (!empty($result[$key])) {
				$result[$key] .= PHP_EOL;
			}

			$result[$key] .= "$value";
		}

		return $result;
	}

	/**
	 * @return string|null
	 */
	private function getDeadline($dateStr, $dateFormat)
	{
		return \DateTime::createFromFormat($dateFormat, $dateStr)
					->setTime(17, 00)
					->format($dateFormat);
	}
}
