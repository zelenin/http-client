<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\Psr7\Psr7Factory;
use Zelenin\HttpClient\Transport\CurlTransport;

final class ClientFactory
{
    /**
     * @var Psr7Factory
     */
    private $factory;

    /**
     * @param Psr7Factory $factory
     */
    public function __construct(Psr7Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param RequestConfig $requestConfig
     *
     * @return Client
     */
    public function createStreamClient(RequestConfig $requestConfig = null): Client
    {
        $middlewareStack = $this->createDefaultMiddlewareStack($requestConfig);

        return new MiddlewareClient($middlewareStack, $this->factory);
    }

    /**
     * @param RequestConfig $requestConfig
     *
     * @return Client
     */
    public function createCurlClient(RequestConfig $requestConfig = null): Client
    {
        $middlewareStack = $this->createDefaultMiddlewareStack($requestConfig);

        return new MiddlewareClient($middlewareStack, $this->factory);
    }

    /**
     * @param RequestConfig $requestConfig
     *
     * @return MiddlewareStack
     */
    private function createDefaultMiddlewareStack(RequestConfig $requestConfig = null): MiddlewareStack
    {
        $requestConfig = $requestConfig ?: new RequestConfig();

        return new MiddlewareStack([
            new UserAgent(sprintf('HttpClient/0.0.1 PHP/%s', PHP_VERSION)),
            new CurlTransport($requestConfig, $this->factory),
            new Deflate()
        ]);
    }
}
