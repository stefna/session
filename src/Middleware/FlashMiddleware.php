<?php declare(strict_types=1);

namespace Stefna\Session\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Stefna\Session\Flash\Messages;
use Stefna\Session\Storage;

final readonly class FlashMiddleware implements MiddlewareInterface
{
	public function __construct(
		private LoggerInterface $logger,
	) {}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$storage = $request->getAttribute(Storage::class);
		if (!$storage instanceof Storage) {
			$this->logger->warning('Can\'t setup flash messages because no session storages configured');
			return $handler->handle($request);
		}
		$flashStorage = new Messages($storage);

		$response = $handler->handle($request->withAttribute(Messages::class, $flashStorage));

		$flashStorage->store();

		return $response;
	}
}
