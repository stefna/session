<?php declare(strict_types=1);

namespace Stefna\Session\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Stefna\Session\Flash\FlashMessages;
use Stefna\Session\SessionStorage;

final readonly class FlashMiddleware implements MiddlewareInterface
{
	private const STORAGE_KEY = 'stefna_message';

	public function __construct(
		private LoggerInterface $logger,
	) {}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$storage = $request->getAttribute(SessionStorage::class);
		if (!$storage instanceof SessionStorage) {
			$this->logger->warning('Can\'t setup flash messages because no session storages configured');
			return $handler->handle($request);
		}
		$flashStorage = new FlashMessages(fn () => $storage->getArray(self::STORAGE_KEY));

		$response = $handler->handle($request->withAttribute(FlashMessages::class, $flashStorage));

		$flashMessages = $flashStorage->toArray();
		if (count($flashMessages)) {
			$storage->set(self::STORAGE_KEY, $flashMessages);
		}
		else {
			$storage->remove(self::STORAGE_KEY);
		}

		return $response;
	}
}
