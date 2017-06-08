<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Middleware
{
    /**
     * @param RequestInterface $request
     * @param MiddlewareDispatcher $dispatcher
     *
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, MiddlewareDispatcher $dispatcher): ResponseInterface;
}
