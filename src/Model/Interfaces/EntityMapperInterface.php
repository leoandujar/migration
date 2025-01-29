<?php

namespace App\Model\Interfaces;

interface EntityMapperInterface
{
	/**
	 *  Generate a hash from attributes.
	 */
	public function hashFromObject(): string;

	/**
	 *  Generate a hash from attributes in the remote resource.
	 */
	public function hashFromRemote($remoteSource): string;

	/**
	 *  Update the entity with the data in the remote.
	 */
	public function populateFromRemote($remoteSource): void;
}
