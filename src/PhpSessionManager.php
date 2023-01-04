<?php declare(strict_types=1);

namespace Stefna\Session;

final class PhpSessionManager implements SessionManager
{
	private function start(): void
	{
		session_start();
	}

	private function getLoader(): callable
	{
		return function () {
			if (session_status() !== PHP_SESSION_ACTIVE) {
				$this->start();
			}
			// wrap session in array object so to not modify _SESSION
			return new \ArrayObject($_SESSION);
		};
	}

	public function getStorage(): SessionStorage
	{
		return new ArraySessionStorage($this->getLoader());
	}

	public function save(SessionStorage $storage): void
	{
		$changedKeys = $storage->getChangedKeys();
		if (!$changedKeys) {
			return;
		}

		foreach ($changedKeys as $key) {
			if (!$storage->has($key)) {
				unset($_SESSION[$key]);
			}
			else {
				$_SESSION[$key] = $storage->get($key);
			}
		}
	}
}
