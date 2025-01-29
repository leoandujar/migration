<?php

namespace App\Apis\CustomerPortal\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	private RequestStack $requestStack;

	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
	}

	/**
	 * @return void
	 */
	public function onJWTCreated(JWTCreatedEvent $event)
	{
		$request = $this->requestStack->getCurrentRequest();

		$payload = $event->getData();
		$payload['ip'] = $request->getClientIp();

		$event->setData($payload);

		$header = $event->getHeader();
		$header['cty'] = 'JWT';

		$event->setHeader($header);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function getSubscribedEvents(): array
	{
		return ['lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated'];
	}
}
