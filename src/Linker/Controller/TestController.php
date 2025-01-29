<?php

namespace App\Linker\Controller;

use App\Service\MercureService;
use App\Apis\Shared\Util\MercureTokenProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Repository\CustomerRepository;
use Twig\Environment;
use App\Service\FileSystem\FileSystemService;

#[Route(path: '/test')]
class TestController extends AbstractController
{
	private EntityManagerInterface $em;
	private CustomerRepository $customerRepository;
	private Environment $env;
	private FileSystemService $fileSystemSrv;

	public function __construct(
		Environment $env,
		CustomerRepository $customerRepository,
		EntityManagerInterface $em,
		FileSystemService $fileSystemSrv,
	) {
		$this->em = $em;
		$this->customerRepository = $customerRepository;
		$this->env = $env;
		$this->fileSystemSrv = $fileSystemSrv;
	}

	#[Route(path: '/mercure', name: 'mercure_render', methods: ['GET'])]
	public function mercureRender(Request $request, Discovery $discovery, MercureTokenProvider $tokenProvider): Response
	{
		$discovery->addLink($request);
		$response = new Response();
		$response->headers->set(
			'set-cookie',
			"mercureAuthorization={$tokenProvider->getJwt()};SameSite=strict;"
		);

		return $this->render('Tests/mercure.html.twig', [], $response);
	}

	#[Route(path: '/mercure/dispatch', name: 'mercure_dispatch', methods: ['GET'])]
	public function mercureDispatch(MercureService $mercureSrv): Response
	{
		$typeEvent = MercureService::TOPIC_FILES;
		$mercureSrv->publish([
			'fileId' => $typeEvent.uniqid(),
			'status' => 'success',
		], 565, $typeEvent);

		return new JsonResponse();
	}
}
