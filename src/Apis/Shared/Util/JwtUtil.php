<?php

namespace App\Apis\Shared\Util;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JwtUtil implements JwtUtilInterface
{
	public const AUTH_KEY_NAME = 'Authorization';
	public const AUTH_TYPE_NAME = 'Bearer';

	private mixed $jwtAlgorithm;
	private mixed $jwtPrivateKey;
	private mixed $jwtPublicKey;

	public function __construct(ParameterBagInterface $bag)
	{
		$this->jwtAlgorithm = $bag->get('jwt_algorithm');
		$this->jwtPrivateKey = $bag->get('jwt_private_key');
		$this->jwtPublicKey = $bag->get('jwt_public_key');
	}

	public function encode(array $tokenData): string
	{
		return JWT::encode($tokenData, file_get_contents($this->jwtPrivateKey), $this->jwtAlgorithm);
	}

	public function decode(string $tokenString): \stdClass
	{
		return JWT::decode($tokenString, new Key(file_get_contents($this->jwtPublicKey), $this->jwtAlgorithm));
	}

	/**
	 * @return false|string|null
	 */
	public function jwtExtract(Request $request): bool|string|null
	{
		if (!$request->headers->has(self::AUTH_KEY_NAME)) {
			return false;
		}

		$authorizationHeader = $request->headers->get(self::AUTH_KEY_NAME);

		if (empty(self::AUTH_TYPE_NAME)) {
			return $authorizationHeader;
		}

		$headerParts = explode(' ', $authorizationHeader);

		if (!(2 === count($headerParts) && 0 === strcasecmp($headerParts[0], self::AUTH_TYPE_NAME))) {
			return false;
		}

		return $headerParts[1];
	}
}
