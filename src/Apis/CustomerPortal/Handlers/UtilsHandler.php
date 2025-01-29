<?php

namespace App\Apis\CustomerPortal\Handlers;

use App\Apis\Shared\Handlers\BaseHandler;
use App\Apis\Shared\Util\UtilsService;
use App\Apis\Shared\Http\Error\ApiError;
use App\Model\Entity\CustomerPriceListLanguageCombination;
use App\Model\Entity\CustomerPriceListRate;
use Doctrine\ORM\EntityManagerInterface;
use App\Apis\Shared\Util\PostmarkService;
use Doctrine\ORM\Mapping\MappingException;
use App\Service\Notification\TeamNotification;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Notification\NotificationService;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\ContactPersonRepository;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UtilsHandler extends BaseHandler
{
	private UtilsService $utilsSrv;
	private EntityManagerInterface $em;
	private QuoteHandler $quoteHandler;
	private PostmarkService $postmarkSrv;
	private ProjectHandler $projectHandler;
	private InvoicesHandler $invoicesHandler;
	private TokenStorageInterface $tokenStorage;
	private NotificationService $notificationSrv;
	private ContactPersonRepository $contactPersonRepository;
	private RequestStack $requestStack;

	public function __construct(
		EntityManagerInterface $em,
		UtilsService $utilsSrv,
		PostmarkService $postmarkSrv,
		TokenStorageInterface $tokenStorage,
		RequestStack $requestStack,
		QuoteHandler $quoteHandler,
		ProjectHandler $projectHandler,
		NotificationService $notificationSrv,
		InvoicesHandler $invoicesHandler,
		ContactPersonRepository $contactPersonRepository
	) {
		parent::__construct($requestStack, $em);
		$this->em = $em;
		$this->tokenStorage = $tokenStorage;
		$this->notificationSrv = $notificationSrv;
		$this->postmarkSrv = $postmarkSrv;
		$this->utilsSrv = $utilsSrv;
		$this->contactPersonRepository = $contactPersonRepository;
		$this->projectHandler = $projectHandler;
		$this->quoteHandler = $quoteHandler;
		$this->invoicesHandler = $invoicesHandler;
		$this->requestStack = $requestStack;
	}

	public function processNotify(array $params): ApiResponse
	{
		$type = $params['type'];
		$target = $params['target'] ?? null;
		switch ($type) {
			case NotificationService::NOTIFICATION_TYPE_TEAM:
				$data = [
					'title' => $params['title'],
					'message' => $params['message'],
					'status' => TeamNotification::STATUS_SUCCESS,
					'date' => new \DateTime(), 'Y-m-d',
				];
				$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_TEAM, $target, $data);
				break;
			case NotificationService::NOTIFICATION_TYPE_PM_EMAIL:
				$entityName = "App\\Model\\Entity\\{$params['entity_name']}";
				$funcName = $params['function_name'];
				try {
					$entity = $this->em->getRepository($entityName)->find($params['entity_id']);
					if (!$entity) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'entity: '.$entityName);
					}

					if (!method_exists($entityName, $funcName)) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'function: '.$funcName);
					}
					$emailAddress = $entity->$funcName();
					$templateId = $this->postmarkSrv->getTemplateId($params['template']);
					$data = $params['variables'];
					$data['template'] = $templateId;
					$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_PM_EMAIL, $emailAddress, $data);
				} catch (MappingException) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'entity_name');
				} catch (\InvalidArgumentException $ex) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'template');
				} catch (\Throwable) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
				}
				break;
			case NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL:
				$entityName = "App\\Model\\Entity\\{$params['entity_name']}";
				$funcName = $params['function_name'];
				try {
					$entity = $this->em->getRepository($entityName)->find($params['entity_id']);
					if (!$entity) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'entity: '.$entityName);
					}

					if (!method_exists($entityName, $funcName)) {
						return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'function: '.$funcName);
					}
					$emailAddress = $entity->$funcName();
					$this->notificationSrv->addNotification(NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL, $emailAddress, [
						'subject' => $params['subject'],
						'from' => $params['from'] ?? null,
						'fromName' => $params['from_name'] ?? null,
						'template' => $params['template'] ?? null,
						'data' => $params['variables'],
					]);
				} catch (MappingException) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'entity_name');
				} catch (\InvalidArgumentException $ex) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'template');
				} catch (\Throwable) {
					return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INTERNAL_ERROR, ApiError::$descriptions[ApiError::CODE_INTERNAL_ERROR]);
				}
				break;
			default:
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'type');
		}

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}

	public function processGlobalSearch(array $dataRequest): ApiResponse
	{
		try {
			$projects = $this->projectHandler->processGetProjects($dataRequest);
		} catch (\Throwable) {
			$projects = new DefaultPaginationResponse();
		}

		try {
			$quotes = $this->quoteHandler->processGetQuotes($dataRequest);
		} catch (\Throwable) {
			$quotes = new DefaultPaginationResponse();
		}

		try {
			$invoices = $this->invoicesHandler->processGetInvoices($dataRequest);
		} catch (\Throwable) {
			$invoices = new DefaultPaginationResponse();
		}

		return new ApiResponse(data: [
			'projects' => $projects->getRaw(),
			'quotes' => $quotes->getRaw(),
			'invoices' => $invoices->getRaw(),
		]);
	}

    public function processGetEstimate(array $dataRequest): ApiResponse
    {
        $customer = $this->getCurrentCustomer();
        $sourceLanguage = $dataRequest['source_language'];
        $totalWords = $dataRequest['total_words'];
        $estimatedResult = 0;
        $minimalCharge = false;
        $skippedLanguages = [];

        foreach ($dataRequest['target_languages'] as $targetLanguage) {
            $languageCombinationNull = null;
            $languageCombinationData = $this->em->getRepository(CustomerPriceListLanguageCombination::class)->getPriceLangCombination(
                $sourceLanguage,
                $targetLanguage,
                $customer->getId()
            );
            if (!$languageCombinationData) {
                $languageCombinationNull = $this->em->getRepository(CustomerPriceListLanguageCombination::class)->getPriceLangCombination(
                    null,
                    null,
                    $customer->getId()
                );
            }

            if (!$languageCombinationData && !$languageCombinationNull) {
                $skippedLanguages[] = $targetLanguage;
                continue;
            }
            $combinationId = $languageCombinationData ? $languageCombinationData->getId() : $languageCombinationNull->getId();
            if ('71' === $sourceLanguage) { // 71 IS EQUAL TO ENGLISH, SO IS FROM
                $calculationUnit = 1;
            } else {
                $calculationUnit = 9;
            }
            $customerRate = $this->em->getRepository(CustomerPriceListRate::class)->findOneBy(
                [
                    'activityType' => 1,                // When needed we must to move to entity constants.
                    'calculationUnit' => $calculationUnit, // When needed we must to move to entity constants.
                    'customerLanguageCombination' => $combinationId,
                ]
            );

            // IF NOT ROW WITH CALCULATION= 9 WE NEED TO TRY WITH 1(ENGLISH)
            if (9 === $calculationUnit && null === $customerRate) {
                $calculationUnit = 1;
                $customerRate = $this->em->getRepository(CustomerPriceListRate::class)->findOneBy(
                    [
                        'activityType' => 1,                // When needed we must to move to entity constants.
                        'calculationUnit' => $calculationUnit, // When needed we must to move to entity constants.
                        'customerLanguageCombination' => $combinationId,
                    ]
                );
            }

            if (!$customerRate) {
                $skippedLanguages[] = $targetLanguage;
                continue;
            }
            $minimalCharge = false;
            if ($totalWords * $customerRate->getRate() < $customerRate->getMinimalCharge()) {
                $estimatedResult += $customerRate->getMinimalCharge();
                $minimalCharge = true;
            } else {
                $estimatedResult += ($totalWords * $customerRate->getRate());
            }
        }

        return new ApiResponse(
            data: [
                'rate' => $estimatedResult > 0 ? strval($this->utilsSrv->amountNumberFormat($estimatedResult)) : null,
                'minimalCharge' => $minimalCharge,
                'skippedLanguages' => $skippedLanguages,
            ]
        );
    }
}
