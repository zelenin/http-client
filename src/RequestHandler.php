<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var array|MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @var MiddlewareInterface
     */
    private $fallbackMiddleware;

    /**
     * @param array|MiddlewareInterface[] $middlewares
     * @param MiddlewareInterface $fallbackMiddleware
     */
    public function __construct(array $middlewares, MiddlewareInterface $fallbackMiddleware)
    {
        $this->middlewares = array_map(function (MiddlewareInterface $middleware) {
            return $middleware;
        }, $middlewares);
        $this->fallbackMiddleware = $fallbackMiddleware;
    }

    /**
     * @inheritdoc
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        if (0 === count($this->middlewares)) {
            return $this->fallbackMiddleware->process($request, $this);
        }

        $middleware = array_shift($this->middlewares);

        return $middleware->process($request, $this);
    }
}
