<?php

namespace App\Apis\CustomerPortal\EventListener;

use App\Apis\Shared\Http\Error\ApiError;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;

class JWTInvalidEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	public function onJWTNotFound(JWTNotFoundEvent $event): void
	{
		$response = new ErrorResponse(
			Response::HTTP_FORBIDDEN,
			ApiError::CODE_AUTHENTICATION_FAILED,
			ApiError::$descriptions[ApiError::CODE_AUTHENTICATION_FAILED]
		);

		$event->setResponse($response);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function getSubscribedEvents(): array
	{
		return ['lexik_jwt_authentication.on_jwt_not_found' => 'onJWTNotFound'];
	}
}
