<?php declare(strict_types=1);

namespace Stefna\Session\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stefna\Session\Manager;
use Stefna\Session\Storage;

final readonly class SessionMiddleware implements MiddlewareInterface
{
	public function __construct(
		private Manager $manager,
	) {}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$storage = $this->manager->getStorage();
		$response = $handler->handle($request->withAttribute(Storage::class, $storage));
		if ($storage->getChangedKeys()) {
			$this->manager->save($storage);
		}
		return $response;
	}
}
