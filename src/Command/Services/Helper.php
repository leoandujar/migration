<?php

namespace App\Command\Services;

use Psr\Log\InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Helper
{
	public const NOT_FOUND = 0;
	public const CREATED = 1;
	public const UPDATED = 2;
	public const NOT_CHANGED = 3;
	public const IGNORED = 4;
	public const DELETED = 5;
	public const PENDING = 6;
	public const DETACHED = 7;

	public const WEEKEND_DAYS = ['Saturday', 'Sunday'];

	public static function resultToString(int $result): string
	{
		return match ($result) {
			static::NOT_FOUND => 'Not found',
			static::CREATED => 'Created',
			static::UPDATED => 'Updated',
			static::NOT_CHANGED => 'Up to date',
			static::IGNORED => 'Ignored',
			static::DELETED => 'Deleted',
			static::PENDING => 'Pending',
			static::DETACHED => 'Detached',
			default => 'unknown status',
		};
	}

	public static function resultToColoredString(int $result): string
	{
		return match ($result) {
			static::NOT_FOUND => '<failure>Not found</failure>',
			static::CREATED => '<success>Created</success>',
			static::UPDATED => '<success>Updated</success>',
			static::NOT_CHANGED => 'Up to date',
			static::IGNORED => '<warning>Ignored</warning>',
			static::DELETED => '<warning>Deleted</warning>',
			default => '<error>unknown status</error>',
		};
	}

	public static function applyFormatting(OutputInterface $output): OutputInterface
	{
		$outputStyle = new OutputFormatterStyle('yellow');
		$output->getFormatter()->setStyle('entname', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green');
		$output->getFormatter()->setStyle('entval', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green', null, ['bold']);
		$output->getFormatter()->setStyle('header', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green', null);
		$output->getFormatter()->setStyle('success', $outputStyle);

		$outputStyle = new OutputFormatterStyle('yellow', null);
		$output->getFormatter()->setStyle('failure', $outputStyle);

		$outputStyle = new OutputFormatterStyle('green', null, ['bold']);
		$output->getFormatter()->setStyle('info', $outputStyle);

		$outputStyle = new OutputFormatterStyle('black', 'yellow', ['bold']);
		$output->getFormatter()->setStyle('warning', $outputStyle);

		$outputStyle = new OutputFormatterStyle('white', 'red', ['bold']);
		$output->getFormatter()->setStyle('error', $outputStyle);

		return $output;
	}

	/**
	 * @return false|int|string
	 */
	public static function getClassName($classname): bool|int|string
	{
		if ($pos = strrpos($classname, '\\')) {
			return substr($classname, $pos + 1);
		}

		return $pos;
	}

	public static function deleteDir($dirPath): void
	{
		if (!is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if ('/' != substr($dirPath, strlen($dirPath) - 1, 1)) {
			$dirPath .= '/';
		}
		$files = glob($dirPath.'*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

	public static function deadline($deadline, ?int $deadlineTime = 17): \DateTime
	{
		$time = $deadlineTime ?? 17;
		if (str_contains($deadline, 'D')) {
			$deadline = str_replace('D', '', $deadline);
			$date = strtotime('now');
			$i = 1;
			while ($deadline >= $i) {
				$date = strtotime('+1 day', $date);
				if (in_array(date('l', $date), self::WEEKEND_DAYS)) {
					++$deadline;
				}
				++$i;
			}
			$dueDate = new \DateInterval(sprintf('P%sD', $deadline));

			return (new \DateTime())
				->add($dueDate)
				->setTime($time, 00);
		}

		return (new \DateTime($deadline))->setTime($time, 00);
	}
}
