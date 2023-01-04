<?php declare(strict_types=1);

namespace Stefna\Session;

use Stefna\Collection\ScalarMap;

interface SessionStorage extends ScalarMap
{
	public function get(string $key): mixed;

	public function set(string $key, mixed $value, bool $overwrite = true): void;

	public function remove(string $key): void;

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
