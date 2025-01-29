<?php

namespace App\Apis\CustomerPortal\Services;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\HeaderAwareJWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTEncodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JWTManagerService implements JWTTokenManagerInterface
{
	protected string $userIdClaim;
	protected JWTEncoderInterface $jwtEncoder;
	protected EventDispatcherInterface $dispatcher;

	public function __construct(JWTEncoderInterface $encoder, EventDispatcherInterface $dispatcher, string $userIdClaim = null)
	{
		$this->jwtEncoder = $encoder;
		$this->dispatcher = $dispatcher;
		$this->userIdClaim = $userIdClaim;
	}

	public function create(UserInterface $user): string
	{
		$payload = ['roles' => $user->getRoles()];
		$this->addUserIdentityToPayload($user, $payload);

		return $this->generateJwtStringAndDispatchEvents($user, $payload);
	}

	public function createFromPayload(UserInterface $user, array $payload): string
	{
		$payload = array_merge(['roles' => $user->getRoles()], $payload);
		$this->addUserIdentityToPayload($user, $payload);

		return $this->generateJwtStringAndDispatchEvents($user, $payload);
	}

	private function generateJwtStringAndDispatchEvents(UserInterface $user, array $payload): string
	{
		$jwtCreatedEvent = new JWTCreatedEvent($payload, $user);
		$this->dispatcher->dispatch($jwtCreatedEvent, Events::JWT_CREATED);

		if ($this->jwtEncoder instanceof HeaderAwareJWTEncoderInterface) {
			$jwtString = $this->jwtEncoder->encode($jwtCreatedEvent->getData(), $jwtCreatedEvent->getHeader());
		} else {
			$jwtString = $this->jwtEncoder->encode($jwtCreatedEvent->getData());
		}

		$jwtEncodedEvent = new JWTEncodedEvent($jwtString);

		$this->dispatcher->dispatch($jwtEncodedEvent, Events::JWT_ENCODED);

		return $jwtString;
	}

	public function decode(TokenInterface $token): array|false
	{
		if (!($payload = $this->jwtEncoder->decode($token->getCredentials()))) {
			return false;
		}

		$event = new JWTDecodedEvent($payload);
		$this->dispatcher->dispatch($event, Events::JWT_DECODED);

		if (!$event->isValid()) {
			return false;
		}

		return $event->getPayload();
	}

	public function parse(string $jwtToken): array
	{
		$payload = $this->jwtEncoder->decode($jwtToken);

		$event = new JWTDecodedEvent($payload);
		$this->dispatcher->dispatch($event, Events::JWT_DECODED);

		if (!$event->isValid()) {
			throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'The token was marked as invalid by an event listener after successful decoding.');
		}

		return $event->getPayload();
	}

	protected function addUserIdentityToPayload(UserInterface $user, array &$payload)
	{
		$accessor = PropertyAccess::createPropertyAccessor();

		$payload[$this->userIdClaim] = $accessor->getValue($user, $this->userIdClaim);
	}

	public function getUserIdentityField(): string
	{
		if (0 === func_num_args() || func_get_arg(0)) {
			trigger_deprecation('lexik/jwt-authentication-bundle', '2.15', 'The "%s()" method is deprecated.', __METHOD__);
		}

		return $this->userIdClaim;
	}

	public function setUserIdentityField($field)
	{
		if (1 >= func_num_args() || func_get_arg(1)) {
			trigger_deprecation('lexik/jwt-authentication-bundle', '2.15', 'The "%s()" method is deprecated.', __METHOD__);
		}

		$this->userIdClaim = $field;
	}

	public function getUserIdClaim(): ?string
	{
		return $this->userIdClaim;
	}
}
