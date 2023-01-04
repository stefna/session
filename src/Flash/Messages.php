<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

use Stefna\Session\SessionStorage;

final class Messages
{
	private const STORAGE_KEY = 'stefna_message';

	/** @var Message[] */
	private array $newMessages = [];

	/** @var Type[] */
	private array $reset;

	public function __construct(
		private readonly SessionStorage $storage,
	) {}

	public function addMessage(Message $message): void
	{
		$this->newMessages[] = $message;
	}

	public function store(): void
	{
		if (!$this->newMessages) {
			return;
		}
		$oldMessages = [];
		if (isset($this->reset)) {
			if (count($this->reset) === 1) {
				$oldMessages = $this->getMessages($this->reset[0]);
			}
			else {
				$oldMessages = $this->getMessages();
			}
		}

		$this->storage->set(self::STORAGE_KEY, array_map(
			fn (Message $msg) => $msg->getArrayCopy(),
			array_merge($this->newMessages, $oldMessages),
		));
		$this->newMessages = [];
		$this->reset = [];
	}

	/**
	 * @return Message[]
	 */
	public function getMessages(Type $type = null): array
	{
		$messages = $this->storage->get(self::STORAGE_KEY);
		if (!is_array($messages)) {
			throw new \RuntimeException('Flash message data is corrupted');
		}
		if (isset($this->reset) && $type) {
			$this->reset[] = $type;
		}
		else {
			$this->reset = $type ? [$type] : Type::ALL;
		}
		$return = [];
		foreach ($messages as $msg) {
			$obj = Message::fromArray($msg);
			if (!$type || $type === $obj->type) {
				$return[] = $obj;
			}
		}
		return $return;
	}
}
