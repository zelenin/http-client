<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

final class MiddlewareDispatcher
{
    /**
     * @var MiddlewareStack
     */
    private $middlewareStack;

    /**
     * @var Middleware
     */
    private $finalMiddleware;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param MiddlewareStack $middlewareStack
     * @param Middleware $finalMiddleware
     */
    public function __construct(MiddlewareStack $middlewareStack, Middleware $finalMiddleware)
    {
        $this->middlewareStack = $middlewareStack;
        $this->finalMiddleware = $finalMiddleware;
        $this->response = new Response();

        $middlewareStack->reset();
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request): ResponseInterface
    {
        if (!$this->middlewareStack->isValid()) {
            return call_user_func($this->finalMiddleware, $request, $this);
        }

        $nextMiddleware = $this->middlewareStack->next();

        $this->response = $nextMiddleware($request, $this);

        return $this->response();
    }

    /**
     * @return ResponseInterface
     */
    public function response(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return MiddlewareDispatcher
     */
    public function withResponse(ResponseInterface $response): self
    {
        $dispatcher = clone $this;
        $dispatcher->response = $response;

        return $dispatcher;
    }
}
