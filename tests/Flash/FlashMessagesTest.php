<?php declare(strict_types=1);

namespace Stefna\Session\Tests\Flash;

use PHPUnit\Framework\TestCase;
use Stefna\Session\Flash\FlashMessage;
use Stefna\Session\Flash\FlashMessages;
use Stefna\Session\Flash\MessageType;

final class FlashMessagesTest extends TestCase
{
	public function testNotReturningRenderedMessagesInToArray(): void
	{
		$messages = new FlashMessages(fn () => [
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'normal',
			],
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'live',
			],
		]);

		$this->assertCount(2, $messages->getMessages());

		$this->assertCount(0, $messages->toArray());
	}

	public function testReturningNotRenderedMessages(): void
	{
		$messages = new FlashMessages(fn () => [
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'normal',
			],
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'live',
			],
		]);

		$this->assertCount(1, $messages->getMessages(MessageType::Live));

		$this->assertCount(1, $messages->toArray());
	}

	public function testAddingNewMessage(): void
	{
		$messages = new FlashMessages(fn () => [
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'normal',
			],
			[
				'message' => 'test',
				'code' => 'notice',
				'type' => 'live',
			],
		]);
		$messages[] = new FlashMessage('Random tests');

		$this->assertCount(1, $messages->getMessages(MessageType::Live));

		$this->assertCount(2, $messages->toArray());
	}

	public function testCorruptLoaderReturnValue(): void
	{
		// @phpstan-ignore-next-line - test for that scenario
		$messages = new FlashMessages(fn () => '');

		$this->expectException(\RuntimeException::class);

		$messages->getMessages();
	}
}
