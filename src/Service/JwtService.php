<?php

declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Sentry\State\Scope;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use function Sentry\captureMessage;
use function Sentry\withScope;

class JwtService
{
	public const AUTH_TYPE_NAME = 'Bearer';
	public const AUTH_KEY_NAME = 'Authorization';

	private string $jwtAlgorithm;
	private string $jwtPublicKey;
	private string $jwtExpiration;
	private string $jwtPrivateKey;
	private RequestStack $requestStack;
	private string $jwtRefreshExpiration;

	public function __construct(
		ParameterBagInterface $bag,
		RequestStack $requestStack
	) {
		$this->requestStack = $requestStack;
		$this->jwtExpiration = $bag->get('jwt_ttl');
		$this->jwtAlgorithm = $bag->get('jwt_algorithm');
		$this->jwtPublicKey = $bag->get('jwt_public_key');
		$this->jwtPrivateKey = $bag->get('jwt_private_key');
		$this->jwtRefreshExpiration = $bag->get('jwt_refresh_ttl');
	}

	public function encode(array $payload, ?string $jwtAlgorithm = null): string
	{
		$jwtAlgorithm = $jwtAlgorithm ?? $this->jwtAlgorithm;
		$payload['ttl'] = $payload['ttl'] ?? $this->jwtExpiration;
		$key = file_exists($this->jwtPrivateKey) ? file_get_contents($this->jwtPrivateKey) : $this->jwtPrivateKey;

		return JWT::encode($payload, $key, $jwtAlgorithm);
	}

	public function decode(string $tokenString, ?string $jwtAlgorithm = null): \stdClass
	{
		$jwtAlgorithm = $jwtAlgorithm ?? $this->jwtAlgorithm;
		$key = file_exists($this->jwtPublicKey) ? file_get_contents($this->jwtPublicKey) : $this->jwtPublicKey;

		return JWT::decode($tokenString, new Key($key, $jwtAlgorithm));
	}

	public function extract(): bool|string|null
	{
		if (!$this->requestStack->getCurrentRequest()->headers->has(self::AUTH_KEY_NAME)) {
			withScope(function (Scope $scope) {
				$scope->setFingerprint(['AUTH LISTENER']);
				captureMessage('NO HEADER AUTH AUT NAME');
			});

			return false;
		}

		$authorizationHeader = $this->requestStack->getCurrentRequest()->headers->get(self::AUTH_KEY_NAME);

		if (empty(self::AUTH_TYPE_NAME)) {
			withScope(function (Scope $scope) {
				$scope->setFingerprint(['AUTH LISTENER']);
				captureMessage('EMPTY authorizationHeader');
			});

			return $authorizationHeader;
		}

		$headerParts = explode(' ', $authorizationHeader);

		if (!(2 === count($headerParts) && 0 === strcasecmp($headerParts[0], self::AUTH_TYPE_NAME))) {
			withScope(function (Scope $scope) {
				$scope->setFingerprint(['AUTH LISTENER']);
				captureMessage('TOKEN DOES NOT CONTAINS TWO PARTS');
			});

			return false;
		}

		if (empty($headerParts[1]) || mb_strlen($headerParts[1]) < 10) {
			withScope(function (Scope $scope) {
				$scope->setFingerprint(['AUTH LISTENER']);
				captureMessage('TOKEN LEN IS LESS THAN 10 CHARS');
			});

			return false;
		}

		return $headerParts[1];
	}

	public function isExpired(mixed $payload): bool
	{
		$now = (new \DateTime('UTC'))->format('U');
		$iat = intval($payload['iat']);
		$ttl = $payload['ttl'];
		$diff = $now - $iat;
		if ($diff >= $ttl) {
			return true;
		}

		return false;
	}

	public function getExpirationDate(mixed $iat, ?int $ttl = null, bool $returnTimestamp = false): int|\DateTimeInterface
	{
		$iat = (new \DateTime('UTC'))->setTimestamp($iat);
		$ttl = $ttl ?? $this->jwtExpiration;
		$expiration = (new \DateInterval(date('Y-m-d H:i:s', time() + $ttl)));
		$result = $iat->add($expiration);
		if ($returnTimestamp) {
			return $result->getTimestamp();
		}

		return $result;
	}

	public function generateToken(array $payload, bool $isRefresh = false): string
	{
		if ($isRefresh) {
			$payload = array_merge($payload, ['isRefresh' => true, 'ttl' => $this->jwtRefreshExpiration]);
		}

		return $this->encode($payload);
	}
}
