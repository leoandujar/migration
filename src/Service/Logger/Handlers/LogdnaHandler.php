<?php

namespace App\Service\Logger\Handlers;

use App\Service\Logger\Formatters\LogdnaFormatter;
use Monolog\Level;
use Monolog\Handler\Curl\Util;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

/**
 * Sends log to Logdna. This handler uses logdna's ingestion api.
 *
 * @see https://docs.logdna.com/docs/api
 *
 * @author Nicolas Vanheuverzwijn
 */
class LogdnaHandler extends AbstractProcessingHandler
{
	private string $ingestion_key;
	private string $hostname;
	private string $ip = '';
	private string $mac = '';
	private $curl_handle;

	public function setIP(string $value): void
	{
		$this->ip = $value;
	}

	public function setMAC(string $value): void
	{
		$this->mac = $value;
	}

	/**
	 * @param string $ingestion_key
	 * @param string $hostname
	 * @param int    $level
	 */
	public function __construct($ingestion_key, $hostname, Level $level = Level::Debug, bool $bubble = true)
	{
		parent::__construct($level, $bubble);

		if (!\extension_loaded('curl')) {
			throw new \LogicException('The curl extension is needed to use the LogdnaHandler');
		}

		$this->ingestion_key = $ingestion_key;
		$this->hostname = $hostname;
		$this->curl_handle = \curl_init();
	}

	protected function write(LogRecord $record): void
	{
		$headers = ['Content-Type: application/json'];
		$data = $record->formatted;

		$url = \sprintf('https://logs.logdna.com/logs/ingest?hostname=%s&mac=%s&ip=%s&now=%s', $this->hostname, $this->mac, $this->ip, $record->datetime->getTimestamp());

		\curl_setopt($this->curl_handle, CURLOPT_URL, $url);
		\curl_setopt($this->curl_handle, CURLOPT_USERPWD, "$this->ingestion_key:");
		\curl_setopt($this->curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		\curl_setopt($this->curl_handle, CURLOPT_POST, true);
		\curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $data);
		\curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $headers);
		\curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);

		Util::execute($this->curl_handle, 5, false);
	}

	/**
	 * @return LogdnaFormatter
	 */
	protected function getDefaultFormatter(): FormatterInterface
	{
		return new LogdnaFormatter();
	}
}
