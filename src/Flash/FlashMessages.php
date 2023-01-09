<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

use Stefna\Collection\AbstractListCollection;

/**
 * @extends AbstractListCollection<FlashMessage>
 */
final class FlashMessages extends AbstractListCollection
{
	/** @var MessageType[] */
	private array $reset;
	/** @var callable(): array<mixed> */
	private $loader;

	/**
	 * @param callable(): array<mixed> $loader
	 */
	public function __construct(callable $loader)
	{
		parent::__construct(FlashMessage::class);
		$this->loader = $loader;
	}

	/**
	 * @return FlashMessage[]
	 */
	public function getMessages(MessageType $type = null): array
	{
		$messages = ($this->loader)();
		if (!is_array($messages)) {
			throw new \RuntimeException('Flash message data is corrupted');
		}
		if (isset($this->reset) && $type) {
			$this->reset[] = $type;
		}
		else {
			$this->reset = $type ? [$type] : MessageType::ALL;
		}
		$return = [];
		foreach ($messages as $msg) {
			$obj = FlashMessage::fromArray($msg);
			if (!$type || $type === $obj->type) {
				$return[] = $obj;
			}
		}
		return $return;
	}

	/**
	 * @return array<array{message: string, code: string, type: string}>
	 */
	public function toArray(): array
	{
		$oldMessages = [];
		if (isset($this->reset)) {
			foreach (MessageType::ALL as $type) {
				if (!in_array($type, $this->reset, true)) {
					$oldMessages[] = $this->getMessages($type);
				}
			}
			$this->reset = [];
		}
		return array_map(
			fn (FlashMessage $msg) => $msg->getArrayCopy(),
			array_merge(parent::toArray(), ...$oldMessages),
		);
	}
}
