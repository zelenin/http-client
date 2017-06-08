<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use SplQueue;

final class MiddlewareStack
{
    /**
     * @var SplQueue
     */
    private $stack;

    /**
     * @param Middleware[] $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->stack = new SplQueue();

        array_walk($middlewares, function (Middleware $middleware) {
            $this->stack->push($middleware);
        });
    }

    public function reset()
    {
        $this->stack->rewind();
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->stack->valid();
    }

    /**
     * @return Middleware
     */
    public function next(): Middleware
    {
        $middleware = $this->stack->current();
        $this->stack->next();

        return $middleware;
    }
}
