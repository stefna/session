<?php declare(strict_types=1);

namespace Stefna\Session;

final class MemorySessionManager implements SessionManager
{
	/** @var \ArrayAccess<string, mixed>|callable */
	private $storage;

	/**
	 * @param callable|\ArrayAccess<string, mixed> $storage
	 */
	public function __construct(
		callable|\ArrayAccess $storage = new \ArrayObject(),
	) {
		$this->storage = $storage;
	}

	public function getStorage(): SessionStorage
	{
		$loader = $this->storage instanceof \ArrayAccess ? fn () => clone $this->storage : $this->storage;
		return new ArraySessionStorage($loader);
	}

	public function save(SessionStorage $storage): void
	{
		$changedKeys = $storage->getChangedKeys();
		if (!$changedKeys) {
			return ;
		}
		if (!$this->storage instanceof \ArrayAccess) {
			$this->storage = ($this->storage)();
		}

		foreach ($changedKeys as $key) {
			if (!$storage->has($key)) {
				unset($this->storage[$key]);
			}
			else {
				$this->storage[$key] = $storage->get($key);
			}
		}
	}
}
