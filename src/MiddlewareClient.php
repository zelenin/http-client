<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

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
    public function send(RequestInterface $request, RequestConfig $requestConfig = null): ResponseInterface
    {
        $requestConfig = $requestConfig ?: new RequestConfig();

        return call_user_func($this->middlewareStack, $request, new Response(), $requestConfig);
    }
}
