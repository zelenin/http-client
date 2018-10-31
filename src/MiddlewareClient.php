<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClient implements ClientInterface
{
    /**
     * @var array|MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @param array|MiddlewareInterface[] $middlewares
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(array $middlewares, ResponseFactoryInterface $responseFactory)
    {
        $this->middlewares = array_map(function (MiddlewareInterface $middleware) {
            return $middleware;
        }, $middlewares);
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $requestHandler = new RequestHandler($this->middlewares, new FallbackMiddleware($this->responseFactory));

        return $requestHandler->handle($request);
    }
}
