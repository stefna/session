<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

enum Type: string
{
	case Normal = 'normal';
	case Live = 'Live';

	public const ALL = [Type::Live, Type::Live];
}
