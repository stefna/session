<?php declare(strict_types=1);

namespace Stefna\Session\Flash;

enum Code: string
{
	case Notice = 'notice';
	case Success = 'success';
	case Warning = 'warning';
	case Error = 'error';
	case FatalError = 'fatalerror';
}
