<?php

namespace App\Connector\Github;

use App\Connector\Github\Response\ResponseInterface;
use GuzzleHttp\Client;
use App\Service\LoggerService;
use GuzzleHttp\Exception\GuzzleException;
use App\Connector\Github\Request\Constant;
use App\Connector\Github\Response\Response;
use App\Connector\Github\Response\HeadResponse;
use App\Connector\Github\Response\PullResponse;
use App\Connector\Github\Response\TreeResponse;
use App\Connector\Github\Request\GetPullRequest;
use App\Connector\Github\Request\GetTreeRequest;
use App\Connector\Github\Response\LabelResponse;
use App\Connector\Github\Response\MergeResponse;
use App\Connector\Github\Request\AddLabelRequest;
use App\Connector\Github\Request\GetLabelRequest;
use App\Connector\Github\Response\CommitResponse;
use App\Connector\Github\Request\MergePullRequest;
use App\Connector\Github\Request\CreatePullRequest;
use App\Connector\Github\Request\CreateTreeRequest;
use App\Connector\Github\Request\CreateLabelRequest;
use App\Connector\Github\Request\PushCommentRequest;
use App\Connector\Github\Response\ReferenceResponse;
use App\Connector\Github\Request\CreateCommitRequest;
use App\Connector\Github\Request\UpdateCommentRequest;
use App\Connector\Github\Request\DeleteReferenceRequest;
use App\Connector\Github\Request\GetLatestCommitRequest;
use Symfony\Component\HttpFoundation\Response as SFResponse;

class GithubService
{
	private Client $client;
	private LoggerService $loggerSrv;

	/**
	 * GithubService constructor.
	 */
	public function __construct(LoggerService $loggerSrv)
	{
		$this->client    = new Client(['base_url' => Constant::GITHUB_API_URL]);
		$this->loggerSrv = $loggerSrv;
		$this->loggerSrv->setSubcontext(self::class);
		$this->loggerSrv->setContext(LoggerService::LOGGER_CONTEXT_WORKFLOW);
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
    //Verify if this return type is correct in pr or review.
	public function getLatestCommit($username, $repository, $token): ResponseInterface|HeadResponse
    {
		$req      = new GetLatestCommitRequest($username, $repository, $token);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Unable to get last commit', $thr);
			throw $thr;
		}

		return HeadResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
    //Verify if this return type is correct in pr or review.
	public function getLatestTree($username, $repository, $token, $shaLatestCommit): ResponseInterface|CommitResponse
    {
		$req      = new GetTreeRequest($username, $repository, $shaLatestCommit, $token);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Unable to get latest tree', $thr);
			throw $thr;
		}

		return CommitResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function createNewTree($username, $repository, $token, $shaBaseTree, $files): ResponseInterface|TreeResponse
	{
		$req      = new CreateTreeRequest($username, $repository, $token, $shaBaseTree, $files);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Unable to create new tree', $thr);
			throw $thr;
		}

		return TreeResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function createNewCommit($username, $repository, $token, $shaLatestCommit, $shaNewTree, $comment): ResponseInterface|CommitResponse
	{
		$req      = new CreateCommitRequest($username, $repository, $token, $shaLatestCommit, $shaNewTree, $comment);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Unable to create new commit', $thr);
			throw $thr;
		}

		return CommitResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function pushCommitForReference($username, $repository, $token, $ref, $shaNewCommit): ResponseInterface|ReferenceResponse
	{
		$req      = new PushCommentRequest($username, $repository, $token, $ref, $shaNewCommit);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			try {
				$response = $this->client->send($req);
			} catch (\Throwable $thr) {
				if (SFResponse::HTTP_UNPROCESSABLE_ENTITY === $thr->getCode()) {
					$req = new UpdateCommentRequest($username, $repository, $token, $ref, $shaNewCommit);
				}
				$response = $this->client->send($req);
			}
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError('Unable to create new commit', $thr);
			throw $thr;
		}

		return ReferenceResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function createNewPullRequest($username, $repository, $token, $head, $title): ResponseInterface|PullResponse
	{
		$req      = new CreatePullRequest($username, $repository, $token, $head, $title);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			if (SFResponse::HTTP_UNPROCESSABLE_ENTITY !== $thr->getCode()) {
				$this->loggerSrv->addError('Unable to create new pull request', $thr);
				throw $thr;
			}
			$this->loggerSrv->addInfo('Pull request already created');
		}

		return PullResponse::decode($response->getBody()->getContents());
	}

	public function mergePullRequest($username, $repository, $token, $pull, $commitTitle): ResponseInterface|MergeResponse
	{
		$req      = new MergePullRequest($username, $repository, $token, $pull, $commitTitle);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('Unable to merge pull request %d', $pull), $thr);
		}

		return MergeResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function getPullRequest($username, $repository, $token, $pull): ResponseInterface|PullResponse
	{
		$req      = new GetPullRequest($username, $repository, $token, $pull);
		$response = new \GuzzleHttp\Psr7\Response();
		try {
			$response = $this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('Unable to fetching pull request %d', $pull), $thr);
			throw $thr;
		}

		return PullResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function deleteReference($username, $repository, $token, $ref): void
	{
		$req = new DeleteReferenceRequest($username, $repository, $token, $ref);
		try {
			$this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('Unable to delete reference %d', $ref), $thr);
			throw $thr;
		}
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function createLabel($username, $repository, $token, $label, $color): ResponseInterface|LabelResponse
	{
		$req = new GetLabelRequest($username, $repository, $token, $label);
		try {
			$response = $this->client->send($req);
		} catch (\Throwable) {
			$this->loggerSrv->addWarning("label doesn't exist trying to create a new one");
			$req = new CreateLabelRequest($username, $repository, $token, $label, $color);
			try {
				$response = $this->client->send($req);
			} catch (\Throwable $thr) {
				$this->loggerSrv->addError(sprintf('Unable to create label %d', $label), $thr);
				throw $thr;
			}
		}

		return LabelResponse::decode($response->getBody()->getContents());
	}

	/**
	 * @throws GuzzleException
	 * @throws \Throwable
	 */
	public function addLabel($username, $repository, $token, $label, $pullNumber): void
	{
		$req = new AddLabelRequest($username, $repository, $token, $label, $pullNumber);
		try {
			$this->client->send($req);
		} catch (\Throwable $thr) {
			$this->loggerSrv->addError(sprintf('Unable to add label %s to pull request %d', $label, $pullNumber), $thr);
			throw $thr;
		}
	}
}
