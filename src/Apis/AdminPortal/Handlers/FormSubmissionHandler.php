<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\DTO\PaginationDto;
use App\Apis\Shared\Http\Response\DefaultPaginationResponse;
use App\Apis\Shared\Util\Factory;
use Mpdf\Mpdf;
use Twig\Environment;
use App\Model\Entity\APForm;
use Mpdf\Output\Destination;
use App\Model\Entity\InternalUser;
use App\Model\Entity\APFormSubmission;
use App\Apis\Shared\Http\Error\ApiError;
use App\Apis\Shared\Traits\UserResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Notification\NotificationService;
use App\Service\FileSystem\FileSystemService;
use App\Apis\Shared\Http\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Apis\Shared\Http\Response\ErrorResponse;
use App\Model\Repository\InternalUserRepository;
use App\Model\Repository\APFormSubmissionRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormSubmissionHandler
{
	use UserResolver;

	private Environment $env;
	private EntityManagerInterface $em;
	private InternalUserRepository $userRepo;
	private SecurityHandler $securityHandler;
	private FileSystemService $fileSystemSrv;
	private NotificationService $notificationSrv;
	private APFormSubmissionRepository $apFormSubmissionRepo;

	public function __construct(
		Environment $env,
		EntityManagerInterface $em,
		SecurityHandler $securityHandler,
		InternalUserRepository $userRepo,
		FileSystemService $fileSystemSrv,
		NotificationService $notificationSrv,
		APFormSubmissionRepository $apFormSubmissionRepo,
	) {
		$this->em = $em;
		$this->env = $env;
		$this->userRepo = $userRepo;
		$this->fileSystemSrv = $fileSystemSrv;
		$this->securityHandler = $securityHandler;
		$this->notificationSrv = $notificationSrv;
		$this->apFormSubmissionRepo = $apFormSubmissionRepo;
	}

	public function processSearch(array $params): ApiResponse
	{
		/** @var InternalUser $user */
		$user = $this->securityHandler->getCurrentUser();

		if (!$user) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}
		$params['userId'] = $user->getId();
		$totalRows = $this->apFormSubmissionRepo->getCountRows($params);
		$paginationDto = new PaginationDto($params['page'], $params['per_page'], $totalRows, $params['sort_order'], $params['sort_by']);
		$dataQuery = array_merge(['start' => $paginationDto->from], $params);
		$sqlResponse = $this->apFormSubmissionRepo->getSearch($dataQuery);
		$result = [];
		foreach ($sqlResponse as $object) {
			$result[] = Factory::apFormSubmissionDtoInstance($object);
		}
		$response = new DefaultPaginationResponse(data: $result);
		$response->setPaginationDto($paginationDto);

		return $response;
	}

	public function processRetrieve(string $id): ApiResponse
	{
		/** @var APFormSubmission $formSubmission */
		$formSubmission = $this->apFormSubmissionRepo->find($id);

		if (!$formSubmission) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form_submission');
		}

		return new ApiResponse(data: Factory::apFormSubmissionDtoInstance($formSubmission));
	}

	public function processSubmit(array $params): ?ApiResponse
	{
		$requireApproval = $params['require_approval'] ?? false;
		$collaborators = $params['collaborators'] ?? [];
		$ownerId = $params['owner'] ?? null;
		$fields = $params['fields'];
		$owner = null;
		/** @var APForm $form */
		$form = $this->em->getRepository(APForm::class)->find($params['form_id']);

		if (!$form) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form');
		}

		/** @var InternalUser $user */
		$submittedBy = $this->securityHandler->getCurrentUser();

		if (!$submittedBy) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
		}

		$status = $requireApproval ? APFormSubmission::STATUS_PENDING : APFormSubmission::STATUS_APPROVED;

		$formSubmission = new APFormSubmission();
		$formSubmission
			->setApForm($form)
			->setSubmittedBy($submittedBy)
			->setApprovedby(null)
			->setStatus($status)
			->setCollaborators($collaborators)
			->setUpdatedAt(null)
			->setSubmittedData($fields);

		if ($ownerId) {
			$owner = $this->userRepo->find($ownerId);

			if (!$owner) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'owner');
			}

			$formSubmission->setOwner($owner);
		}

		$this->em->persist($formSubmission);
		$this->em->flush();
		$to = [];
		if ($requireApproval) {
			foreach ($form->getApprovers() as $id) {
				$approver = $this->userRepo->find($id);
				$to[] = $approver->getEmail();
			}
		}

		if ($owner) {
			array_push($to, $owner->getEmail());
		}

		foreach ($collaborators as $id) {
			$collaborator = $this->userRepo->find($id);
			array_push($to, $collaborator->getEmail());
		}
		if (!empty($to)) {
			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
				$to,
				[
					'subject' => $formSubmission->getApForm()->getName(),
					'data' => [
						'name' => 'Team Member',
						'id' => $formSubmission->getId(),
						'status' => $status,
						'approved' => false,
						'content' => "A new entry that requires your input has been added to the {$form->getName()}. Please log into the Admin Portal to see the details.",
					],
				]
			);
		}

		return new ApiResponse(data: ['id' => $formSubmission->getId()]);
	}

	public function processUpdate(array $params): ?ApiResponse
	{
		/** @var APFormSubmission $formSubmission */
		$formSubmission = $this->apFormSubmissionRepo->find($params['id']);

		$ownerId = $params['owner'] ?? null;

		if (!$formSubmission) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'form_submission');
		}

		if (!in_array($params['status'], [APFormSubmission::STATUS_APPROVED, APFormSubmission::STATUS_PENDING, APFormSubmission::STATUS_DENIED], true)) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_INVALID_VALUE, ApiError::$descriptions[ApiError::CODE_INVALID_VALUE], 'status');
		}

		if (isset($params['collaborators'])) {
			$formSubmission
				->setCollaborators($params['collaborators']);
		}

		if (isset($params['owner'])) {
			$owner = $this->userRepo->find($params['owner']);

			if (!$owner) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'owner');
			}
			$formSubmission
				->setOwner($owner);
		}

		if (isset($params['fields'])) {
			$formSubmission
				->setSubmittedData($params['fields'])
				->setUpdatedAt(new \DateTime());
		}

		$approvedBy = null;

		if (APFormSubmission::STATUS_APPROVED === $params['status']) {
			/** @var InternalUser $user */
			$approvedBy = $this->securityHandler->getCurrentUser();

			if (!$approvedBy) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'user');
			}

			$formSubmission
				->setApprovedby($approvedBy)
				->setStatus($params['status'])
				->setUpdatedAt(new \DateTime());

			$this->notificationSrv->addNotification(
				NotificationService::NOTIFICATION_TYPE_MAILER_EMAIL,
				$formSubmission->getSubmittedBy()?->getEmail(),
				[
					'subject' => $formSubmission->getApForm()->getName(),
					'data' => [
						'name' => $formSubmission->getSubmittedBy()?->getFirstName(),
						'id' => $formSubmission->getId(),
						'status' => $formSubmission->getStatus(),
						'approved' => true,
					],
				]
			);
		}

		$this->em->persist($formSubmission);
		$this->em->flush();

		return new ApiResponse(data: Factory::apFormSubmissionDtoInstance($formSubmission));
	}

	public function processDownloadViewFile(array $params): string|ErrorResponse|BinaryFileResponse|null
	{
		if (1 === count($params['ids'])) {
			$submissionId = array_shift($params['ids']);

			/** @var APFormSubmission $submission */
			$submission = $this->apFormSubmissionRepo->find($submissionId);

			if (!$submission) {
				return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'submission');
			}

			$tpl = $this->env->createTemplate($submission->getApForm()?->getTemplate()?->getContent());
			$content = $tpl->render([
				'data' => $submission->getSubmittedData(),
				'submittedAt' => $submission->getSubmittedAt(),
			]);
			$mpdf = new Mpdf();
			$mpdf->setAutoBottomMargin = 'stretch';
			$mpdf->setAutoTopMargin = 'stretch';
			$mpdf->WriteHTML($content);
			$prefixName = preg_replace('/\s+/', '_', $submission->getApForm()->getName());
			$fileName = "{$prefixName}_$submissionId.pdf";

			return $mpdf->OutputHttpDownload($fileName);
		}

		$zipper = new \ZipArchive();
		$zipName = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.uniqid('form_submission_');
		$zipper->open($zipName, \ZipArchive::CREATE);
		$countFailed = 0;
		$folderName = 'form-submission'.uniqid();
		$this->fileSystemSrv->createTempDir($folderName);
		$folderName = $this->fileSystemSrv->filesPath.DIRECTORY_SEPARATOR.$folderName;
		foreach ($params['ids'] as $id) {
			$mpdf = new Mpdf();
			$mpdf->setAutoBottomMargin = 'stretch';
			$mpdf->setAutoTopMargin = 'stretch';
			/** @var APFormSubmission $submission */
			$submission = $this->apFormSubmissionRepo->find($id);

			if (!$submission) {
				++$countFailed;
				continue;
			}
			$tpl = $this->env->createTemplate($submission->getApForm()?->getTemplate()?->getContent());
			$content = $tpl->render([
				'data' => $submission->getSubmittedData(),
			]);
			$mpdf->WriteHTML($content);
			$prefixName = preg_replace('/\s+/', '_', $submission->getApForm()->getName());
			$fileName = "{$prefixName}_$id.pdf";
			try {
				$mpdf->Output("$folderName/$fileName", Destination::FILE);
				$zipper->addFile("$folderName/$fileName", $fileName);
			} catch (\Throwable) {
				++$countFailed;
				continue;
			}
		}

		$zipper->close();
		if (count($params['ids']) === $countFailed) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'params');
		}
		$this->fileSystemSrv->deleteDir($folderName);
		$response = new BinaryFileResponse($zipName);
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

		return $response;
	}

	public function processViewFile(string $id): BinaryFileResponse|ApiResponse|string|ErrorResponse|null
	{
		/** @var APFormSubmission $submission */
		$submission = $this->apFormSubmissionRepo->find($id);

		if (!$submission) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'submission');
		}
		$mpdf = new Mpdf();
		$mpdf->setAutoBottomMargin = 'stretch';
		$mpdf->setAutoTopMargin = 'stretch';

		$tpl = $this->env->createTemplate($submission->getApForm()?->getTemplate()?->getContent());
		$content = $tpl->render([
			'data' => $submission->getSubmittedData(),
			'submittedAt' => $submission->getSubmittedAt(),
		]);
		$mpdf->WriteHTML($content);
		$now = (new \DateTime('now'))->format('Y-m-d_H-i-s');
		$fileName = "{$submission->getApForm()->getName()}_$now.pdf";

		return $mpdf->Output($fileName, Destination::INLINE);
	}

	public function processDelete(array $params): ApiResponse
	{
		$submission = $this->apFormSubmissionRepo->find($params['id']);

		if (!$submission) {
			return new ErrorResponse(Response::HTTP_BAD_REQUEST, ApiError::CODE_NOT_FOUND, ApiError::$descriptions[ApiError::CODE_NOT_FOUND], 'submission');
		}

		$this->em->remove($submission);
		$this->em->flush();

		return new ApiResponse(code: Response::HTTP_NO_CONTENT);
	}
}
