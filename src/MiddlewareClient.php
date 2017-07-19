<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClient implements Client
{
    /**
     * @var MiddlewareStack
     */
    private $middlewareStack;

    /**
     * @param MiddlewareStack $middlewareStack
     */
    public function __construct(MiddlewareStack $middlewareStack)
    {
        $this->middlewareStack = $middlewareStack;
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $dispatcher = new MiddlewareDispatcher($this->middlewareStack, new FinalMiddleware());

        return call_user_func($dispatcher, $request);
    }
}
