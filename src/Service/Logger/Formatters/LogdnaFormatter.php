<?php

namespace App\Service\Logger\Formatters;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class LogdnaFormatter extends JsonFormatter
{
	public function __construct(int $batchMode = self::BATCH_MODE_NEWLINES, bool $appendNewline = false, bool $ignoreEmptyContextAndExtra = false, bool $includeStacktraces = false)
	{
		parent::__construct($batchMode, $appendNewline, $ignoreEmptyContextAndExtra, $includeStacktraces);
	}

	protected function normalizeRecord(LogRecord $record): array
	{
		$date = new \DateTime();

		$json = [
			'lines' => [
				[
					'timestamp' => $date->getTimestamp(),
					'line' => $record->message,
					'app' => $record->channel,
					'level' => $record->level->toPsrLogLevel(),
					'meta' => $record->context,
				],
			],
		];

		return $this->normalize($json);
	}
}
