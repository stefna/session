<?php declare(strict_types=1);

namespace Stefna\Session\Tests;

use PHPUnit\Framework\TestCase;
use Stefna\Session\ArraySessionStorage;

final class StorageTest extends TestCase
{
	public function testLazyLoadOfData(): void
	{
		$storage = new ArraySessionStorage(fn () => $this->fail('Should never be called'));

		$this->assertEmpty($storage->getChangedKeys());
	}

	public function testFindingKey(): void
	{
		$testValue = 'random';
		$testKey = 'test';
		/** @var \ArrayObject<string, mixed> $data */
		$data = new \ArrayObject([$testKey => $testValue]);
		$storage = new ArraySessionStorage(fn () => $data);

		$this->assertTrue($storage->has($testKey));
		$this->assertSame($testValue, $storage->get($testKey));
	}

	public function testRemoveFromStorage(): void
	{
		$testKey = 'test';
		/** @var \ArrayObject<string, mixed> $data */
		$data = new \ArrayObject([$testKey => 'random']);
		$storage = new ArraySessionStorage(fn () => $data);

		$storage->remove($testKey);

		$this->assertFalse($storage->has($testKey));
		$this->assertCount(1, $storage->getChangedKeys());
		$this->assertSame([$testKey], $storage->getChangedKeys());
	}

	public function testPreventingOverwritingData(): void
	{
		$testKey = 'test';
		/** @var \ArrayObject<string, mixed> $data */
		$data = new \ArrayObject([$testKey => 'random']);
		$storage = new ArraySessionStorage(fn () => $data);

		try {
			$storage->set($testKey, 1, false);
			$this->fail();
		}
		catch (\BadMethodCallException $e) {
			$this->assertTrue(true);
		}
		finally {
			$this->assertEmpty($storage->getChangedKeys());
		}
	}

	public function testOverwritingData(): void
	{
		$newValue = 'testOverwritingData';
		$testKey = 'test';
		/** @var \ArrayObject<string, mixed> $data */
		$data = new \ArrayObject([$testKey => 'random']);
		$storage = new ArraySessionStorage(fn () => $data);

		$storage->set($testKey, $newValue);

		$this->assertSame([$testKey], $storage->getChangedKeys());
		$this->assertSame($newValue, $storage->getString($testKey));
	}
}
