<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

enum MessageType: string
{
	case Normal = 'normal';
	case Live = 'live';

	public const ALL = [MessageType::Live, MessageType::Normal];
}
