<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Resource;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestHandlerInterface;

final class ClosureWrapMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * @param callable $closure
     */
    public function __construct(callable $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return call_user_func($this->closure, $request, $handler);
    }
}
