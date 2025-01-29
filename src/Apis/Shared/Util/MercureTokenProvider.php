<?php

namespace App\Apis\Shared\Util;

use App\Service\JwtService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;

class MercureTokenProvider implements TokenProviderInterface
{
	private JwtService $jwtSrv;
	private ParameterBagInterface $parameterBag;

	public function __construct(
		JwtService $jwtSrv,
		ParameterBagInterface $parameterBag,
	) {
		$this->jwtSrv = $jwtSrv;
		$this->parameterBag = $parameterBag;
	}

	public function getJwt(): string
	{
		$jwtKey = $this->parameterBag->get('mercure_jwt_secret');
		$payload = [
			'mercure' => [
				'publish' => ['*'],
				'subscribe' => ['files/{id}', 'commands/{id}'],
			],
		];

		return $this->jwtSrv->encode($payload);
	}
}
