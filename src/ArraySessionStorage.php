<?php declare(strict_types=1);

namespace Stefna\Session;

use Stefna\Collection\ScalarMapTrait;

final class ArraySessionStorage implements SessionStorage
{
	use ScalarMapTrait;

	/** @var callable */
	private $loader;
	/** @var array<string, mixed>|\ArrayAccess<string, mixed> */
	private array|\ArrayAccess $data;
	/** @var string[] */
	private array $changedKeys = [];

	/**
	 * @param callable(): (array<string, mixed>|\ArrayAccess<string, mixed>) $loader
	 */
	public function __construct(callable $loader)
	{
		$this->loader = $loader;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		$data = $this->getAllData();
		return $data[$key] ?? $default;
	}

	public function has(string $key): bool
	{
		return isset($this->getAllData()[$key]);
	}

	public function set(string $key, mixed $value, bool $overwrite = true): void
	{
		$this->getAllData();
		if (!$overwrite && isset($this->data[$key])) {
			throw new \BadMethodCallException('Can\'t overwrite existing value');
		}
		$this->data[$key] = $value;
		$this->changedKeys[] = $key;
	}

	public function remove(string $key): void
	{
		$this->getAllData();
		unset($this->data[$key]);
		$this->changedKeys[] = $key;
	}

	/**
	 * @param array<string, mixed> $default
	 * @return array<string, mixed>
	 */
	public function getArray(string $key, array $default = []): array
	{
		$value = $this->getAllData()[$key] ?? $default;
		if (!is_array($value)) {
			return $default;
		}

		return $value;
	}

	/**
	 * @return string[]
	 */
	public function getChangedKeys(): array
	{
		return $this->changedKeys;
	}

	/**
	 * @return array<string, mixed>|\ArrayAccess<string, mixed>
	 */
	private function getAllData(): array|\ArrayAccess
	{
		if (!isset($this->data)) {
			$this->data = ($this->loader)();
		}
		return $this->data;
	}
}
