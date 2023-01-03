<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

final readonly class Message
{
	/**
	 * @param array{message: string, code: string, type: string} $data
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			$data['message'],
			Code::tryFrom($data['code']) ?? Code::Notice,
			Type::tryFrom($data['type']) ?? Type::Normal,
		);
	}

	public function __construct(
		public string $message,
		public Code $code = Code::Notice,
		public Type $type = Type::Normal,
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
