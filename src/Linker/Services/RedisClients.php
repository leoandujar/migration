<?php

namespace App\Linker\Services;

class RedisClients
{
	public const string SESSION_KEY_QBO_QUEUE = 'qbo_queue';
	public const string SESSION_KEY_QBO_TOKEN = 'qbo_token';
	public const string SESSION_KEY_QUEUE_ALIAS = 'qbo_entities_update_queue';
	public const string SESSION_KEY_QUEUE_ALIAS_PAGE = 'qbo_entities_pages';
	public const string SESSION_KEY_NOTIFICATIONS = 'notification_queue';
	public const string SESSION_KEY_STRIPE_PAYMENTS = 'stripe-webhook_queue';
	public const string SESSION_KEY_HUBSPOT_COMMAND_QUEUE = 'hubspot_command_queue';
	public const string SESSION_KEY_HUBSPOT_WEBHOOK_QUEUE = 'hubspot_webhook_queue';
	public const string SESSION_KEY_POSTMARK_WEBHOOK_QUEUE = 'postmark_webhook_queue';
	public const string SESSION_KEY_PENDING_FILES = 'pending_files_queue';
	public const string SESSION_KEY_PENDING_FILES_ORDER = 'pending_files_queue_order';
	public const string SESSION_KEY_AWAITING_FILES = 'awaiting_files_queue';
	public const string SESSION_KEY_AWAITING_WORKFLOWS = 'awaiting_workflows_queue';
	public const string SESSION_KEY_COMMANDS_QUEUE = 'awaiting_commands_queue';
	public const string SESSION_KEY_PROJECTS_QUOTES = 'projects_quotes';
	public const string SESSION_KEY_PROJECTS_QUOTES_URGENT = 'projects_files_urgent';
	public const string SESSION_KEY_PROJECTS_QUOTES_HIGH = 'projects_files_high';
	public const string SESSION_KEY_PROJECTS_QUOTES_NORMAL = 'projects_files_normal';
	public const string SESSION_KEY_PROJECTS_QUOTES_ERROR = 'projects_files_error';
	public const string SESSION_KEY_PROJECT_QUOTE_PARAMS = 'project_quote_params';
	public const string SESSION_KEY_BOOSTLINGO_TOKEN = 'boostlingo_token';
	public const string SESSION_KEY_BOOSTLINGO_COMMAND_QUEUE = 'boostlingo_command_queue';
	public const string SESSION_KEY_RULES_COMMAND_QUEUE = 'rules_command_queue';
	public const string SESSION_KEY_XTRF_AUTH_INFO = 'xtrf_auth_info';

	public const int DEFAULT_PAGE_SIZE = 500;
	public const int DEFAULT_QUEUE_COUNT_FAILURE = 10;
	public const int QUEUE_COUNT_FAILURE_NOTIFICATIONS = 60;

	public \Redis $redisMainDB;
	public \Redis $redisPasswordDB;

	public function __construct($redisMainDB, $redisPasswordDB)
	{
		$this->redisMainDB = $redisMainDB;
		$this->redisPasswordDB = $redisPasswordDB;
	}
}
