<?php

namespace App\Apis\CustomerPortal\EventListener;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;

class JWTExpiredEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	/**
	 * @return void
	 */
	public function onJWTExpired(JWTExpiredEvent $event)
	{
		$response = new ErrorResponse(
			Response::HTTP_UNAUTHORIZED,
			ApiError::CODE_TOKEN_EXPIRED,
			ApiError::$descriptions[ApiError::CODE_TOKEN_EXPIRED]
		);
		$event->setResponse($response);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function getSubscribedEvents(): array
	{
		return ['lexik_jwt_authentication.on_jwt_expired' => 'onJWTExpired'];
	}
}
