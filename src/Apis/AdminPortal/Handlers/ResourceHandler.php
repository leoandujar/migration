<?php

namespace App\Apis\AdminPortal\Handlers;

use App\Apis\Shared\Util\Factory;
use App\Model\Entity\Category;
use App\Model\Repository\XtrfLanguageRepository;
use App\Apis\Shared\Http\Response\ApiResponse;
use App\Constant\Constants;
use Doctrine\ORM\EntityManagerInterface;

class ResourceHandler
{
	private XtrfLanguageRepository $xtrfLanguageRepository;
	private EntityManagerInterface $em;

	public function __construct(
		XtrfLanguageRepository $xtrfLanguageRepository,
		EntityManagerInterface $em,
	) {
		$this->xtrfLanguageRepository = $xtrfLanguageRepository;
		$this->em = $em;
	}

	public function processGetLanguages(): ApiResponse
	{
		$languages = $this->xtrfLanguageRepository->findBy(['active' => true], ['name' => 'ASC']);
		$result = [];
		foreach ($languages as $language) {
			$result[] = Factory::languageDtoInstance($language);
		}

		return new ApiResponse(data: $result);
	}

	public function processGetCategoryList(): ApiResponse
	{
		$categories = $this->em->getRepository(Category::class)->findBy(['active' => true], ['name' => 'ASC']);

		$result = [];
		foreach ($categories as $category) {
			$result[] = Factory::categoryDtoInstance($category);
		}

		return new ApiResponse(data: $result);
	}

	public function processGetWfTypeList(): ApiResponse
	{
		return new ApiResponse(data: Constants::getWfTypes());
	}

	public function processGetWfNotificationTypeList(): ApiResponse
	{
		return new ApiResponse(data: Constants::getWfNotificationTypes());
	}

	public function processGetWfDiskList(): ApiResponse
	{
		return new ApiResponse(data: Constants::getWfDisks());
	}

	public function processGetXtrfSubscriptionTypeList(): ApiResponse
	{
		return new ApiResponse(Constants::getXtrfSubscriptionTypeList());
	}
}
