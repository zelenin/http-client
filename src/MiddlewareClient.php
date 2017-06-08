<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class MiddlewareClient implements Client
{
    /**
     * @var MiddlewareDispatcher
     */
    private $dispatcher;

    /**
     * @param MiddlewareStack $middlewareStack
     */
    public function __construct(MiddlewareStack $middlewareStack)
    {
        $this->dispatcher = new MiddlewareDispatcher($middlewareStack, new FinalMiddleware());
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return call_user_func($this->dispatcher, $request);
    }
}
