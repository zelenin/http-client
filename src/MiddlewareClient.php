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
    public function send(RequestInterface $request, RequestConfig $requestConfig = null): ResponseInterface
    {
        $requestConfig = $requestConfig ?: new RequestConfig();

        return call_user_func(
            $this->middlewareStack,
            $request->wit,
            $this->factory->createResponse($this->factory->createStream(fopen('php://temp', 'rb+')), 200, []),
            $requestConfig
        );
    }
}
