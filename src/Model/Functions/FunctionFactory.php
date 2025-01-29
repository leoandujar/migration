<?php
	
	namespace App\Model\Functions;
	
	use Doctrine\DBAL\Platforms\AbstractPlatform;
	use Doctrine\DBAL\Platforms\MySQLPlatform;
	use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
	use Doctrine\ORM\Query\QueryException;
	class FunctionFactory
	{
		
		public static function create(
			AbstractPlatform $platform,
			string $functionName,
			array $parameters
		): PlatformFunctionNode {
			if ($platform instanceof PostgreSQLPlatform) {
				$platformName = 'postgresql';
			} elseif ($platform instanceof MySQLPlatform) {
				$platformName = 'mysql';
			} else {
				throw QueryException::syntaxError(
					\sprintf(
						'Not supported platform "%s"',
						$platform::class
					)
				);
			}
			
			$className = __NAMESPACE__
				. '\\Platform\\Functions\\'
				. static::classify(\strtolower($platformName))
				. '\\'
				. static::classify(\strtolower($functionName));
			
			if (!\class_exists($className)) {
				throw QueryException::syntaxError(
					\sprintf(
						'Function "%s" does not supported for platform "%s"',
						$functionName,
						$platformName
					)
				);
			}
			
			return new $className($parameters);
		}
		
		private static function classify($word): array|string
		{
			return \str_replace([' ', '_', '-'], '', \ucwords($word, ' _-'));
		}
	}
