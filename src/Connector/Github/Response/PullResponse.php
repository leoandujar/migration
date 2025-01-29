<?php

namespace App\Connector\Github\Response;

class PullResponse implements ResponseInterface
{
	public ?string $url;
	public ?int $id;
	public ?string $nodeID;
	public ?string $diffUrl;
	public ?string $patchUrl;
	public ?string $issueUrl;
	public ?string $commitsUrl;
	public ?string $reviewCommentsUrl;
	public ?string $reviewCommentUrl;
	public ?string $commentsUrl;
	public ?string $statusesUrl;
	public ?int $number;
	public ?string $state;
	public ?bool $locked;
	public ?string $title;
	public ?bool $mergeable;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance                    = new self();
		$instance->url               = $data->url ?? null;
		$instance->id                = $data->id ?? null;
		$instance->nodeID            = $data->node_id ?? null;
		$instance->diffUrl           = $data->diff_url ?? null;
		$instance->patchUrl          = $data->patch_url ?? null;
		$instance->issueUrl          = $data->issue_url ?? null;
		$instance->commitsUrl        = $data->commits_url ?? null;
		$instance->reviewCommentsUrl = $data->review_comments_url ?? null;
		$instance->reviewCommentUrl  = $data->review_comment_url ?? null;
		$instance->commentsUrl       = $data->comments_url ?? null;
		$instance->statusesUrl       = $data->statuses_url ?? null;
		$instance->number            = $data->number ?? null;
		$instance->state             = $data->state ?? null;
		$instance->locked            = $data->locked ?? null;
		$instance->title             = $data->title ?? null;
		$instance->mergeable         = $data->mergeable ?? null;

		return $instance;
	}
}
