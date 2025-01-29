<?php

declare(strict_types=1);

namespace App\Service;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Exception\GuzzleException;
use App\Service\Logger\Handlers\LogdnaHandler;
use App\Service\Logger\Handlers\LokiHandler;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Monolog\Handler\WhatFailureGroupHandler;

class LoggerService
{
	public const LOGGER_CONTEXT_DEFAULT = 'App';
	public const LOGGER_CONTEXT_API_CLIENT_PORTAL = 'Api Client Portal';
	public const LOGGER_CONTEXT_API_ADMIN_PORTAL = 'Api Admin Portal';
	public const LOGGER_CONTEXT_COMMANDS = 'Commands';
	public const LOGGER_CONTEXT_LINKERS = 'Linkers';
	public const LOGGER_CONTEXT_CONNECTORS = 'Connectors';
	public const LOGGER_CONTEXT_WORKFLOW = 'Workflows';
	public const LOGGER_CONTEXT_BUCKET = 'Buckets';
	public const LOGGER_CONTEXT_WEBHOOKS = 'Webhooks';

	public const LOGGER_SUB_CONTEXT_WF_XTM_TM = 'Xtm TM';
	public const LOGGER_SUB_CONTEXT_WF_ZIP_CREATE = 'Create Zip';
	public const LOGGER_SUB_CONTEXT_WF_XTM_GITHUB = 'Xtm Github';
	public const LOGGER_SUB_CONTEXT_WF_XTM_PROJECT = 'Xtm Project';
	public const LOGGER_SUB_CONTEXT_WF_XTRF_PROJECT = 'Xtrf Project';
	public const LOGGER_SUB_CONTEXT_WF_EMAIL_PARSING = 'Email Parsing';
	public const LOGGER_SUB_CONTEXT_WF_ATTESTATION = 'Attestation';
	public const LOGGER_SUB_CONTEXT_WF_XTRF_QBO = 'Xtrf-Qbo';
	public const LOGGER_SUB_CONTEXT_WF_BL_XTRF = 'Bl-Xtrf';

	private LoggerInterface|Logger $logger;
	private LoggerInterface|Logger $loggerLk;
	private string $context = self::LOGGER_CONTEXT_DEFAULT;
	private string $subcontext = self::LOGGER_CONTEXT_DEFAULT;

	public function __construct(ParameterBagInterface $bag)
	{
		$lokiHandler = new WhatFailureGroupHandler(
			[
				new LokiHandler(
					[
						'entrypoint' => $bag->get('app.logs.loki_url'),
						'context' => [
							// Set here your globally applicable context variables
						],
						'labels' => [
							// Set here your globally applicable labels
						],
						'client_name' => $bag->get('app.logs.host'), // Here set a unique identifier for the client host
						// Optional : if you're using basic auth to authentify
						'auth' => [
							'basic' => [$bag->get('app.logs.loki_user'), $bag->get('app.logs.loki_password')],
						],
						// Optional : Override the default curl options with custom values
						'curl_options' => [
							CURLOPT_CONNECTTIMEOUT_MS => 5000,
							CURLOPT_TIMEOUT_MS => 6000,
						],
					]
				),
			]
		);
		$this->loggerLk = new Logger($bag->get('app.logs.app'));
		$this->loggerLk->pushHandler($lokiHandler);

		$this->logger = new Logger($bag->get('app.logs.app'));
		$this->logger->pushHandler(new LogdnaHandler($bag->get('app.logs.logdna_key'), $bag->get('app.logs.host')));
	}

	/**
	 * @param array|\Throwable|GuzzleException $data
	 */
	public function addInfo(?string $message, $data = null): void
	{
		$context = $this->genericMsg($data);
		$this->logger->info($message, $context);
		$this->loggerLk->info($message, $context);
	}

	/**
	 * @param array|\Throwable|GuzzleException $data
	 */
	public function addWarning(string $message, $data = null): void
	{
		$context = $this->genericMsg($data);
		$this->logger->warning($message, $context);
		$this->loggerLk->warning($message, $context);
	}

	/**
	 * @param array|\Throwable|GuzzleException $data
	 */
	public function addNotice(string $message, $data = null): void
	{
		$context = $this->genericMsg($data);
		$this->logger->notice($message, $context);
		$this->loggerLk->notice($message, $context);
	}

	/**
	 * @param array|\Throwable|GuzzleException $data
	 */
	public function addError(string $message, $data = null): void
	{
		$context = $this->genericMsg($data);
		$this->logger->error($message, $context);
		$this->loggerLk->error($message, $context);
	}

	/**
	 * @param array|\Throwable|GuzzleException $data
	 */
	public function addCritical(string $message, $data = null): void
	{
		$context = $this->genericMsg($data);
		$this->logger->critical($message, $context);
		$this->loggerLk->critical($message, $context);
	}

	private function genericMsg($data = null): array
	{
		$context = [
			'Category' => $this->context ?? 'Not defined',
			'Subcategory' => $this->subcontext ?? 'Not defined',
		];
		if (null !== $data && is_object($data)) {
			$context += [
				'message' => $data->getMessage(),
				'code' => $data->getCode(),
				'file' => $data->getFile(),
				'line' => $data->getLine(),
			];
		} elseif (is_array($data)) {
			$context += [
				'data' => $data,
			];
		}

		return $context;
	}

	public function alert($event = null, ?\Throwable $thr = null): void
	{
		$strEvent = 'Text not specified';
		if ($event instanceof Event) {
			$strEvent = sprintf(
				'Workflow (id: "%s") performed transition "%s" from "%s" to "%s"',
				$event->getSubject()->getId(),
				$event->getTransition()->getName(),
				implode(', ', array_keys($event->getMarking()->getPlaces())),
				implode(', ', $event->getTransition()->getTos())
			);
		}
		$this->addError($strEvent, $thr);
	}

	public function setContext(string $context): void
	{
		$this->context = $context;
	}

	public function setSubcontext(string $subcontext): void
	{
		$this->subcontext = $subcontext;
	}
}
