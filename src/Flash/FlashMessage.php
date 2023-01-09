<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

final readonly class FlashMessage
{
	/**
	 * @param array{message: string, code: string, type: string} $data
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			$data['message'],
			FlashCode::tryFrom($data['code']) ?? FlashCode::Notice,
			MessageType::tryFrom($data['type']) ?? MessageType::Normal,
		);
	}

	public function __construct(
		public string $message,
		public FlashCode $code = FlashCode::Notice,
		public MessageType $type = MessageType::Normal,
	) {}

	/**
	 * @return array{message: string, code: string, type: string}
	 */
	public function getArrayCopy(): array
	{
		return [
			'message' => $this->message,
			'code' => $this->code->value,
			'type' => $this->type->value,
		];
	}
}
