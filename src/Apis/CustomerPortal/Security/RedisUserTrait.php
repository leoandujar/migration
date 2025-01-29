<?php

namespace App\Apis\CustomerPortal\Security;

use App\Apis\Shared\Facade\AppFacade;
use App\Linker\Services\RedisClients;

trait RedisUserTrait
{
	public function retrieveSessionId()
	{
		$existingData = $this->retrieveXtrfUserData();

		return $existingData['xtrf_session_id'] ?? null;
	}

	public function retrieveXtrfUserData()
	{
		$result = [];
		$userId = $this->requestStack->getCurrentRequest()->attributes->get('user_id');
		if ($this->requestStack->getCurrentRequest() && $userId) {
			$result = $this->redisClients->redisMainDB->hget(RedisClients::SESSION_KEY_XTRF_AUTH_INFO, $userId);
			if ($result) {
				$result = unserialize($result);
			}
		}

		return $result;
	}

	public function saveXtrfUserData($userId, string $username, $xtrfSessionId): void
	{
		$this->redisClients->redisMainDB->hmset(RedisClients::SESSION_KEY_XTRF_AUTH_INFO, [$userId => serialize([
			'id' => $userId,
			'username' => $username,
			'xtrf_session_id' => $xtrfSessionId,
		])]);
	}

	public function retrieveSwitchCustomerData(): ?string
	{
		$jwt = $this->requestStack->getCurrentRequest()->attributes->get('logged_jwt');
		if ($jwt && AppFacade::getInstance()->jwtSrv) {
			$decodeToken = (array) AppFacade::getInstance()->jwtSrv->decode($jwt);

			return $decodeToken['active_office'] ?? null;
		}

		return null;
	}
}
