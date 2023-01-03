<?php declare(strict_types=1);

namespace Stefna\Session;

final class SessionStorage implements Storage
{
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

	/**
	 * @return array<string, mixed>|\ArrayAccess<string, mixed>
	 */
	private function load(): array|\ArrayAccess
	{
		if (!isset($this->data)) {
			$this->data = ($this->loader)();
		}
		return $this->data;
	}

	public function get(string $key, mixed $default = null): mixed
	{
		$data = $this->load();
		return $data[$key] ?? $default;
	}

	public function has(string $key): bool
	{
		return isset($this->load()[$key]);
	}

	public function set(string $key, mixed $value, bool $overwrite = true): void
	{
		$this->load();
		if (!$overwrite && isset($this->data[$key])) {
			throw new \BadMethodCallException('Can\'t overwrite existing value');
		}
		$this->data[$key] = $value;
		$this->changedKeys[] = $key;
	}

	public function remove(string $key): void
	{
		$this->load();
		unset($this->data[$key]);
		$this->changedKeys[] = $key;
	}

	/**
	 * @phpstan-return ($default is string ? string : string|null)
	 */
	public function getString(string $key, ?string $default = null): ?string
	{
		$value = $this->load()[$key] ?? $default;
		if (!is_scalar($value)) {
			return $default;
		}
		return (string)$value;
	}

	/**
	 * @phpstan-return ($default is int ? int : int|null)
	 */
	public function getInt(string $key, ?int $default = null): ?int
	{
		$value = $this->load()[$key] ?? $default;
		if (is_numeric($value)) {
			return (int)$value;
		}
		return $default;
	}

	/**
	 * @phpstan-return ($default is float ? float : float|null)
	 */
	public function getFloat(string $key, ?float $default = null): ?float
	{
		$value = $this->load()[$key] ?? $default;
		if (is_numeric($value)) {
			return (float)$value;
		}
		return $default;
	}

	/**
	 * @phpstan-return ($default is bool ? bool : bool|null)
	 */
	public function getBool(string $key, ?bool $default = null): ?bool
	{
		$value = $this->load()[$key] ?? $default;
		if (is_bool($value) || $value === null) {
			return $value;
		}

		if (!is_scalar($value)) {
			return $default;
		}

		return in_array($value, [
			1,
			'1',
			'on',
			'true',
		], true);
	}

	/**
	 * @param array<string, mixed> $default
	 * @return array<string, mixed>
	 */
	public function getArray(string $key, array $default = []): array
	{
		$value = $this->load()[$key] ?? $default;
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
}
