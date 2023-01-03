<?php declare(strict_types=1);

namespace Stefna\Session;

interface Storage
{
	public function get(string $key): mixed;

	public function has(string $key): bool;

	public function set(string $key, mixed $value, bool $overwrite = true): void;

	public function remove(string $key): void;

	/**
	 * @phpstan-return ($default is int ? int : int|null)
	 */
	public function getInt(string $key, ?int $default = null): ?int;

	/**
	 * @param array<string, mixed> $default
	 * @return array<string, mixed>
	 */
	public function getArray(string $key, array $default = []): array;

	/**
	 * @return string[]
	 */
	public function getChangedKeys(): array;
}
