<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SplQueue;

final class MiddlewareStack
{
    /**
     * @var SplQueue
     */
    private $stack;

    /**
     * @var callable
     */
    private $dispatcher;

    /**
     * @var callable
     */
    private $finalMiddleware;

    /**
     * @param callable[] $items
     */
    public function __construct(array $items)
    {
        $this->stack = new SplQueue();

        array_walk($items, function (callable $middleware) {
            $this->stack->push($middleware);
        });

        $this->dispatcher = function (
            RequestInterface $request,
            ResponseInterface $response,
            RequestConfig $requestConfig
        ) {
            if (!$this->stack->valid()) {
                return call_user_func($this->finalMiddleware, $request, $response, $requestConfig);
            }

            $middleware = $this->stack->current();
            $this->stack->next();

            return call_user_func($middleware, $request, $response, $requestConfig, $this->dispatcher);
        };

        $this->finalMiddleware = function (
            RequestInterface $request,
            ResponseInterface $response,
            RequestConfig $requestConfig
        ) {
            return $response;
        };
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param RequestConfig $requestConfig
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, RequestConfig $requestConfig)
    {
        $this->stack->rewind();

        return call_user_func($this->dispatcher, $request, $response, $requestConfig);
    }
}
