# Sessions for psr-15 and psr-7

[![Build Status](https://github.com/stefna/session/actions/workflows/continuous-integration.yml/badge.svg?branch=main)](https://github.com/stefna/session/actions/workflows/continuous-integration.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/stefna/session.svg)](https://packagist.org/packages/stefna/session)
[![Software License](https://img.shields.io/github/license/stefna/session.svg)](LICENSE)

The package helps in working with sessions in a PSR-7/PSR-15 environment.

It also provides a flash message service which helps use one-time messages.

## Requirements

PHP 8.2 or higher.

## Installation

```bash
composer require stefna/session
```

### Session

In order to maintain a session between requests you need to add `SessionMiddleware`
to middleware collection.

Then you will have access to the session storage in the request object and can
be retrieved
```php
<?php
$session = $request->getAttribute(\Stefna\Session\SessionStorage::class); 
```

### Flash Messages

You need to add `FlashMiddleware` to your middleware runner, and it needs to be 
added after the `SessionMiddleware`. 

Then you will have access to the flash messages in the request object and can be
retried
```php
<?php
$messageCollection = $request->getAttribute(\Stefna\Session\Flash\FlashMessages::class); 
```

## Concept

The main concept of package is the separation of using the session from 
persisting the session.

This is achieved with lazy loading everything. The session is not started
until someone needs something from the session.

## Usage of session

```php
<?php
use Psr\Http\Message\ServerRequestInterface;

class Action
{
	public function __invoke(ServerRequestInterface $request)
	{
		$session = $request->getAttribute(\Stefna\Session\SessionStorage::class);
		if ($session->getBool('loggedIn')) {
			$session->set('loggedIn', false);
		}
		elseif ($session->has('blocked')) {
			$session->remove('blocked');
		}
	}
}
```

## Usage of flash messages

### Add flash messages
```php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Stefna\Session\Flash\FlashMessage;
use Stefna\Session\Flash\FlashMessages;
use Stefna\Session\Flash\MessageType;

class Action
{
	public function __invoke(ServerRequestInterface $request)
	{
		$flashMessages = $request->getAttribute(FlashMessages::class);
		$flashMessages->add(new FlashMessage('Stuff happened', type: MessageType::Live));
	}
}
```

### Get flash messages

Getting the flash messages means that they will be removed from storage.

```php
<?php
use Psr\Http\Message\ServerRequestInterface;
use Stefna\Session\Flash\FlashMessages;
use Stefna\Session\Flash\MessageType;

class Action
{
	public function __invoke(ServerRequestInterface $request)
	{
		$flashMessages = $request->getAttribute(FlashMessages::class);
		$messages = $flashMessages->getMessages(MessageType::Live);
		
		$this->renderGrowMessages($messages);
	}
}
```

## Contribute

We are always happy to receive bug/security reports and bug/security fixes

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

