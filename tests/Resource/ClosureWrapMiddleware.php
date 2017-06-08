<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Resource;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware;
use Zelenin\HttpClient\MiddlewareDispatcher;

final class ClosureWrapMiddleware implements Middleware
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * @var bool
     */
    private $preDispatcher;

    /**
     * @param callable $closure
     */
    public function __construct(callable $closure, bool $preDispatcher)
    {
        $this->closure = $closure;
        $this->preDispatcher = $preDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, MiddlewareDispatcher $dispatcher): ResponseInterface
    {
        if ($this->preDispatcher) {
            $response = call_user_func($this->closure, $request, $dispatcher);
            $dispatcher = $dispatcher->withResponse($response);

            return $dispatcher($request);
        }

        $dispatcher($request);

        return call_user_func($this->closure, $request, $dispatcher);
    }
}
