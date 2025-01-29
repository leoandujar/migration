<?php

namespace App\Apis\CustomerPortal\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	private RequestStack $requestStack;

	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}

	/**
	 * @return void
	 */
	public function onJWTDecoded(JWTDecodedEvent $event)
	{
		$request = $this->requestStack->getCurrentRequest();

		$payload = $event->getPayload();

		if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
			$event->markAsInvalid();
		}
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function getSubscribedEvents(): array
	{
		return ['lexik_jwt_authentication.on_jwt_decoded' => 'onJWTDecoded'];
	}
}
