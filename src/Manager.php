<?php declare(strict_types=1);

namespace Stefna\Session;

interface Manager
{
	public function getStorage(): Storage;

	public function save(Storage $storage): void;
}
