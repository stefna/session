<?php declare(strict_types=1);

namespace Stefna\Session;

interface SessionManager
{
	public function getStorage(): SessionStorage;

	public function save(SessionStorage $storage): void;
}
