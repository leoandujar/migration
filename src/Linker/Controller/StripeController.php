<?php

namespace App\Linker\Controller;

use App\Apis\Shared\Listener\PublicEndpoint;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Service\LoggerService;
use App\Linker\Services\RedisClients;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Model\Repository\CustomerInvoiceRepository;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route(path: '/webhooks/stripe')]
class StripeController extends AbstractController
{
	private LoggerService $loggerSrv;
	private ParameterBagInterface $parameterBag;
	private RedisClients $redisClients;

	public function __construct(
		LoggerService $loggerSrv,
		RedisClients $redisClients,
		ParameterBagInterface $parameterBag,
	) {
		$this->loggerSrv = $loggerSrv;
		$this->parameterBag = $parameterBag;
		$this->redisClients = $redisClients;
	}

	/**
	 * @return string
	 */
	#[PublicEndpoint]
	#[Route(path: '/test/{id}', name: 'test_details_invoice', methods: ['GET'])]
	public function details($id, CustomerInvoiceRepository $invoiceRepo): Response
	{
		$invoice = $invoiceRepo->find($id);

		return $this->render('Stripe/test_invoice_details.twig', [
			'data' => $invoice,
		]);
	}

	#[PublicEndpoint]
	#[Route(path: '/', name: 'stripe_webhook', methods: ['GET', 'POST'])]
	public function stripePaymentCallback(Request $request): Response
	{
		$signature = $this->parameterBag->get('stripe.webhook_signature');
		Stripe::setApiKey($this->parameterBag->get('stripe.sk'));

		$receivedSignature = $request->headers->get('stripe-signature');

		$event = null;
		try {
			$payload = file_get_contents('php://input');
			if (false === $payload || empty($payload)) {
				throw new \Exception('Error getting content from: php://input');
			}

			$event = Webhook::constructEvent($payload, $receivedSignature, $signature);
		} catch (\UnexpectedValueException|SignatureVerificationException $e) {
			return new Response($e->getMessage() ?? null, Response::HTTP_BAD_REQUEST);
		}
		switch ($event->type) {
			case 'payment_intent.succeeded':
			case 'checkout.session.completed':
				$paymentIntent = $event->data->object;

				$data = (object) [
					'countFailed' => 0,
					'xtrfCreated' => false,
					'qboCreated' => true,
					'type' => $event->type,
					'data' => (object) $paymentIntent->jsonSerialize(),
				];

				$this->redisClients->redisMainDB->rpush(RedisClients::SESSION_KEY_STRIPE_PAYMENTS, serialize($data));
				break;
			default:
				$this->loggerSrv->addError("Stripe return unknown event to callback: type=>$event->type, id=>$event->id");
		}

		return new Response();
	}
}
