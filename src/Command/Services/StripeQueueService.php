<?php

namespace App\Command\Services;

use App\Connector\Qbo\Dto\InvoiceDto;
use App\Service\LoggerService;
use App\Connector\Qbo\QboConnector;
use App\Connector\Stripe\StripeConnector;
use App\Model\Entity\CustomerInvoice;
use App\Connector\Xtrf\XtrfConnector;
use App\Model\Repository\CustomerInvoiceRepository;

class StripeQueueService
{
	private LoggerService $loggerSrv;
	private CustomerInvoiceRepository $invoiceRepository;
	private XtrfConnector $xtrfConnector;
	private QboConnector $qboConnector;
	private StripeConnector $stripeConnector;

	public function __construct(
		QboConnector $qboConnector,
		XtrfConnector $xtrfConnector,
		CustomerInvoiceRepository $invoiceRepository,
		LoggerService $loggerSrv,
		StripeConnector $stripeConnector
	) {
		$this->loggerSrv = $loggerSrv;
		$this->invoiceRepository = $invoiceRepository;
		$this->xtrfConnector = $xtrfConnector;
		$this->qboConnector = $qboConnector;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_COMMANDS);
		$this->stripeConnector = $stripeConnector;
	}

	public function processPayload(object $payload): mixed
	{
		$data = $payload->data;
		$metadata = $data->metadata;
		if (empty($metadata)) {
			$this->loggerSrv->addNotice("Unable to process Stripe Payment due lack of invoice id. Payment# $data->id");

			return null;
		}
		if (empty($metadata['invoice_id'])) {
			$this->loggerSrv->addNotice("Unable to process Stripe Payment due lack of invoice id. Payment# $data->id");

			return null;
		}

		$invoice = $this->invoiceRepository->find($metadata['invoice_id']);

		if (!$invoice) {
			$this->loggerSrv->addError("Unable to find CustomerInvoice with id {$metadata['invoice_id']} in Stripe Service");

			return null;
		}

		if ($payload->xtrfCreated) {
			$this->loggerSrv->addInfo("Stripe Payment already success in Home Api for invoice {$metadata['invoice_id']}. Not need to send it.");
		} else {
			if (CustomerInvoice::INVOICE_STATUS_SENT !== $invoice->getState() || CustomerInvoice::PAYMENT_STATUS_UNPAID !== $invoice->getPaymentState()) {
				$this->loggerSrv->addInfo("It looks like invoice with ID=>{$invoice->getId()} is already paid.");
			} else {
				$this->loggerSrv->addInfo("Preparing request from Stripe Payment to Home Api for invoice {$metadata['invoice_id']}.");

				$dataRequest = [
					'amount' =>  $data->amount / 100,
					'paymentDate' => ['time' => $data->created * 1000],
					'paymentMethodId' => 2,
					'notes' => $data->description ?? '',
				];
				$xtrfCreateResponse = $this->xtrfConnector->createInvoicePayment($invoice->getId(), $dataRequest);
				if (!$xtrfCreateResponse->isSuccessfull()) {
					$this->loggerSrv->addCritical("Unable to create payment on XTRF for Id {$metadata['invoice_id']} in Stripe Services.");
				}

				$payload->xtrfCreated = $xtrfCreateResponse->isSuccessfull();
			}
		}

		if ($payload->qboCreated) {
			$this->loggerSrv->addInfo("Stripe Payment already success in Qbo Api for invoice {$metadata['invoice_id']}. Not need to send it.");
		} else {
			$customer = $invoice->getCustomer();
			if (empty($customer->getQboId())) {
				$this->loggerSrv->addCritical("Unable to send request to QBO API due customer invoice has not QBO Id value for invoice {$metadata['invoice_id']} in Stripe Services.");

				return $payload;
			}
			$qboInvoceDto = new InvoiceDto(
				$customer->getQboId(),
				[
					[
						'Amount' => $data->amount / 100,
						'LinkedTxn' => [
							[
								'TxnId' => $invoice->getQboId(),
								'TxnType' => 'Invoice',
							], ],
					],
                ],
				$data->amount / 100,
				null,
				null,
                $data->finalDate ?? null,
                $data->customFields ?? null
			);
			$qboCreateResponse = $this->qboConnector->createInvoicePayment($qboInvoceDto->toArray());

			if (null === $qboCreateResponse) {
				$this->loggerSrv->addCritical("Unable to create payment on QBO for Id {$metadata['invoice_id']} in Stripe Services.");

				return $payload;
			}

			$payload->qboCreated = true;
		}

		if ($payload->qboCreated && $payload->xtrfCreated) {
			return true;
		}

		return $payload;
	}

	public function processCheckoutPayload(object $payload): mixed
	{
		$lineItems = $this->stripeConnector->retrieveSessionLineItems($payload->data->id);

		$lineItemData = $lineItems->data;

		$xtrfCreated = $qboCreated = 0;

		foreach ($lineItemData as $lineItem) {
			$invoice = $this->invoiceRepository->findOneBy(['finalNumber' => $lineItem->description]);

			if (!$invoice) {
				$this->loggerSrv->addError("Unable to find CustomerInvoice with id {$lineItem->description} in Stripe Service");

				return null;
			}

			if ($payload->xtrfCreated) {
				$this->loggerSrv->addInfo("Stripe Payment already success in Home Api for invoice {$lineItem->description}. Not need to send it.");
			} else {
				if (CustomerInvoice::INVOICE_STATUS_SENT !== $invoice->getState() || CustomerInvoice::PAYMENT_STATUS_UNPAID !== $invoice->getPaymentState()) {
					$this->loggerSrv->addInfo("It looks like invoice with ID=>{$invoice->getId()} is already paid.");
				} else {
					$this->loggerSrv->addInfo("Preparing request from Stripe Payment to Home Api for invoice {$lineItem->description}.");

					$dataRequest = [
						'amount' =>  $lineItem->amount_total / 100,
						'paymentDate' => ['time' => $lineItem->price->created * 1000],
						'paymentMethodId' => 2,
						'notes' => $lineItem->description ?? '',
					];
					$xtrfCreateResponse = $this->xtrfConnector->createInvoicePayment($invoice->getId(), $dataRequest);
					if (!$xtrfCreateResponse->isSuccessfull()) {
						$this->loggerSrv->addCritical("Unable to create payment on XTRF for Id {$invoice->getId()} in Stripe Services.");
					}
					$xtrfCreated = $xtrfCreated + 1;
				}
			}

			if ($payload->qboCreated) {
				$this->loggerSrv->addInfo("Stripe Payment already success in Qbo Api for invoice {$invoice->getId()}. Not need to send it.");
			} else {
				$customer = $invoice->getCustomer();
				if (empty($customer->getQboId())) {
					$this->loggerSrv->addCritical("Unable to send request to QBO API due customer invoice has not QBO Id value for invoice {$invoice->getId()} in Stripe Services.");

					return $payload;
				}
				$qboInvoceDto = new InvoiceDto(
					$customer->getQboId(),
					[
						[
							'Amount' => $lineItem->amount_total / 100,
							'LinkedTxn' => [
								[
									'TxnId' => $invoice->getQboId(),
									'TxnType' => 'Invoice',
								], ],
						], ],
					$lineItem->amount_total / 100,
					null,
					null,
                    $lineItem->price->finalDate ?? null,
                    $lineItem->price->customFields ?? null
				);
				$qboCreateResponse = $this->qboConnector->createInvoicePayment($qboInvoceDto->toArray());

				if (null === $qboCreateResponse) {
					$this->loggerSrv->addCritical("Unable to create payment on QBO for Id {$invoice->getId()} in Stripe Services.");

					return $payload;
				}

				$qboCreated = $qboCreated + 1;
			}
		}

		if ($xtrfCreated == count($lineItemData) && $qboCreated == count($lineItemData)) {
			return true;
		}

		return $payload;
	}
}
