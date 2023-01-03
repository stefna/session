<?php declare(strict_types=1);

namespace Stefna\Session\Tests;

use PHPUnit\Framework\TestCase;
use Stefna\Session\MemoryManager;
use Stefna\Session\SessionStorage;
use Stefna\Session\Storage;

final class ManagerTest extends TestCase
{
	public function testChangingOriginalDataDontPropagate(): void
	{
		$testValue = 1;
		$testKey = 'test';
		$data = new \ArrayObject([
			$testKey => $testValue,
		]);
		$manager = new MemoryManager($data);

		$storage = $manager->getStorage();

		$this->assertSame($testValue, $storage->getInt($testKey));

		$data[$testKey] = 2;

		$this->assertSame($testValue, $storage->getInt($testKey));
	}

	public function testChangingStorageDontAffectOriginalData(): void
	{
		$testValue = 1;
		$testKey = 'test';
		$newKey = 'newKey';
		$data = new \ArrayObject([
			$testKey => $testValue,
		]);
		$manager = new MemoryManager($data);

		$storage = $manager->getStorage();
		$storage->set($testKey, 2);
		$storage->set($newKey, 3);

		$this->assertFalse(isset($data[$newKey]));
		$this->assertTrue($storage->has($newKey));
	}

	public function testDataPersistedAfterSave(): void
	{
		$testValue = 1;
		$testKey = 'test';
		$newKey = 'newKey';
		$deleteKey = 'deleteKey';
		$data = new \ArrayObject([
			$testKey => $testValue,
			$deleteKey => $testValue,
		]);
		$manager = new MemoryManager($data);

		$storage = $manager->getStorage();
		$storage->set($testKey, 2);
		$storage->set($newKey, 3);
		$storage->remove($deleteKey);

		$manager->save($storage);

		$this->assertSame(2, $data[$testKey]);
		$this->assertSame(3, $data[$newKey]);
		$this->assertFalse(isset($data[$deleteKey]));
	}

	public function testHappyPathForSaving(): void
	{
		$manager = new MemoryManager(fn () => $this->fail('Shouldn\'t be called'));
		$storage = $manager->getStorage();

		$this->assertEmpty($storage->getChangedKeys());
		$manager->save($storage);
	}
}