<?php

namespace App\Connector\Stripe;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Service\LoggerService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Stripe\Collection;

class StripeConnector
{
	private LoggerService $loggerSrv;
	private string $baseUrl;

	/**
	 * StripeConnector constructor.
	 */
	public function __construct(
		ParameterBagInterface $parameterBag,
		LoggerService $loggerSrv
	) {
		$this->loggerSrv = $loggerSrv;
		Stripe::setApiKey($parameterBag->get('stripe.sk'));
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_CONNECTORS);
		$this->baseUrl = $parameterBag->get('app.cp_url');
	}

	/**
	 * @throws ApiErrorException
	 */
	public function createSession(
		?string $reference,
		array $lines,
		string $description,
		string $returnCallbackPath,
		int $userId,
		?string $email,
		string $paidBy = 'customer'
	): Session {
		$this->loggerSrv->addInfo('Starting Stripe session');

		$returnCallbackUrl = $this->baseUrl.$returnCallbackPath.'?session_id={CHECKOUT_SESSION_ID}';

		return Session::create([
			'payment_method_types' => ['card'],
			'ui_mode' => 'embedded',
			'customer_email' => $email,
			'line_items' => $lines,
			'mode' => 'payment',
			'client_reference_id' => $reference,
			'return_url' => $returnCallbackUrl,
			'payment_intent_data' => [
				'metadata' => [
					'user_id' => $userId,
					'paid_by' => $paidBy,
					'description' => $description,
				],
			],
		]);
	}

	/**
	 * @throws ApiErrorException
	 */
	public function retrieveSession(
		string $sessionId
	): Session {
		$this->loggerSrv->addInfo('Retrieving Stripe session');

		return Session::retrieve($sessionId);
	}

	/**
	 * @throws ApiErrorException
	 */
	public function retrieveSessionLineItems(
		string $sessionId
	): Collection {
		$this->loggerSrv->addInfo('Retrieving Stripe session');

		return Session::allLineItems($sessionId);
	}

	/**
	 * @throws ApiErrorException
	 */
	public function createPaymentIntent(
		string $referenceId,
		int $amount,
		string $currency,
		string $description
	): PaymentIntent {
		$this->loggerSrv->addInfo('Starting Stripe session');
		$response = null;

		return PaymentIntent::create([
			'amount' => $amount,
			'currency' => $currency,
			'payment_method_types' => ['card'],
			'description' => $description,
			'metadata' => [
				'invoice_id' => $referenceId,
				'description' => $description,
			],
		]);
	}
}
