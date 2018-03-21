<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClient implements ClientInterface
{
    /**
     * @var array|MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @param array|MiddlewareInterface[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->middlewares = array_map(function (MiddlewareInterface $middleware) {
            return $middleware;
        }, $middlewares);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $requestHandler = new RequestHandler($this->middlewares, new FallbackMiddleware());

        return $requestHandler->handle($request);
    }
}
