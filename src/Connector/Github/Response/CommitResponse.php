<?php

namespace App\Connector\Github\Response;

class CommitResponse implements ResponseInterface
{
	public ?string $sha;
	public ?string $type;
	public ?string $url;
	public ?string $htmlURL;
	public ResponseInterface $author;
	public ?ResponseInterface $committer;
	public ?ResponseInterface $tree;

	public static function decode($data): ResponseInterface
	{
		if (is_string($data)) {
			$data = json_decode($data);
		}
		$instance            = new self();
		$instance->url       = $data->url ?? null;
		$instance->type      = $data->type ?? null;
		$instance->sha       = $data->sha ?? null;
		$instance->htmlURL   = $data->html_url ?? null;
		$instance->author    = AuthorResponse::decode($data->author ?? []);
		$instance->committer = CommitterResponse::decode($data->committer ?? []);
		$instance->tree      = TreeResponse::decode($data);

		return $instance;
	}
}
