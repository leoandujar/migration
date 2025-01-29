<?php

declare(strict_types=1);

namespace App\Service;

class RegexService
{
	public const REGEX_TYPE_EMAIL = 'EMAIL';
	public const REGEX_TYPE_FAX = 'FAX';
	public const REGEX_TYPE_IDENTIFIER = 'IDENTIFIER';
	public const REGEX_TYPE_NUMBER = 'NUMBER';
	public const REGEX_TYPE_NAME = 'NAME';
	public const REGEX_TYPE_PASSWORD = 'PASSWORD';
	public const REGEX_TYPE_PHONE = 'PHONE';
	public const REGEX_TYPE_STRING = 'STRING';
	public const REGEX_TYPE_HTML = 'HTML';
	public const REGEX_TYPE_TEXT = 'TEXT';
	public const REGEX_TYPE_TOKEN = 'TOKEN';
	public const REGEX_TYPE_USERNAME = 'USERNAME';
	public const REGEX_TYPE_NUID = 'NUID';
	public const REGEX_TYPE_CRON = 'CRON';
	public const REGEX_TYPE_URL = 'URL';
	public const REGEX_TYPE_CUSTOM = 'CUSTOM';

	public static $htmlTagsAllowed = [
		'p',
		'blockquote',
		'a',
		'u',
		's',
		'em',
		'ul',
		'li',
		'br',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'strong',
	];

	public static function match(string $type, string $text, $customRegex = null, array &$matches = null): bool
	{
		$pattern = match ($type) {
			self::REGEX_TYPE_EMAIL => '/(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))/iD',
			self::REGEX_TYPE_FAX => '/^(\+?\d{1,}(\s?|\-?)\d*(\s?|\-?)\(?\d{2,}\)?(\s?|\-?)\d{3,}\s?\d{3,})/i',
			self::REGEX_TYPE_IDENTIFIER => '/^[a-zA-Z0-9_-]{1,50}+$/i',
			self::REGEX_TYPE_NUMBER => '/^[0-9]{1,30}+$/i',
			self::REGEX_TYPE_NAME => '/^[a-zA-Z.,\'’ ]{1,60}+$/i',
			self::REGEX_TYPE_PASSWORD => '/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
			self::REGEX_TYPE_PHONE => '/^\+*\d{1,7}\s*[\d]{3,20}\s*[\d]{3,20}$/',
			self::REGEX_TYPE_STRING => '/^[\p{L}\p{N}\s\.\-\*\_\+\:\&\(\)]+$/i',
			self::REGEX_TYPE_TEXT => '/^[\p{L}\p{N}\s\.\/\-\_\+\:\(\)]+$/i',
			self::REGEX_TYPE_TOKEN => '/^\S{5,}\z/',
			self::REGEX_TYPE_USERNAME => '/^\S{5,}\z/',
			self::REGEX_TYPE_NUID => '/[a-zA-Z\d]{7}/',
			self::REGEX_TYPE_CRON => '/^(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?))(,(((\*|(\d\d?))(\/\d\d?)?)|(\d\d?\-\d\d?)))*\s(\?|(((\*|(\d\d?L?))(\/\d\d?)?)|(\d\d?L?\-\d\d?L?)|L|(\d\d?W))(,(((\*|(\d\d?L?))(\/\d\d?)?)|(\d\d?L?\-\d\d?L?)|L|(\d\d?W)))*)\s(((\*|(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))(\/\d\d?)?)|((\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\-(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)))(,(((\*|(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))(\/\d\d?)?)|((\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\-(\d|10|11|12|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC))))*\s(((\*|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)(\/\d\d?)?)|(([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?\-([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?#([1-5]))(,(((\*|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)(\/\d\d?)?)|(([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?\-([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?)|([0-7]|MON|TUE|WED|THU|FRI|SAT|SUN)L?#([1-5])))*$/i',
			self::REGEX_TYPE_URL => '/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\(\)\*\+,;=.]+$/i',
			self::REGEX_TYPE_CUSTOM => $customRegex,
			default => null,
		};

		return (bool) preg_match($pattern, $text, $matches);
	}

	public static function isValidHtmlString(string $text, array $allowedTags = null): bool
	{
		try {
			if (!mb_strlen($text)) {
				return true;
			}
			$config = \HTMLPurifier_Config::createDefault();
			$purifier = new \HTMLPurifier($config);
			$purifier->purify($text);
		} catch (\Throwable $thr) {
			return false;
		}

		return true;
	}
}
