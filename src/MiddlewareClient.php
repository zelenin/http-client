<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Psr7\Psr7Factory;

final class MiddlewareClient implements Client
{
    /**
     * @var MiddlewareStack
     */
    private $middlewareStack;

    /**
     * @var Psr7Factory
     */
    private $factory;

    /**
     * @param MiddlewareStack $middlewareStack
     * @param Psr7Factory $factory
     */
    public function __construct(MiddlewareStack $middlewareStack, Psr7Factory $factory)
    {
        $this->middlewareStack = $middlewareStack;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return call_user_func($this->middlewareStack, $request, $this->factory->createResponse($this->factory->createStream(fopen('php://temp', 'rb+')), 200, []));
    }
}
