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
    public function create(RequestConfig $requestConfig = null): Client
    {
        $requestConfig = $requestConfig ?: new RequestConfig();

        $middlewareStack = new MiddlewareStack([
            new UserAgent(),
            new CurlTransport($requestConfig, $this->factory),
            new Deflate($this->factory)
        ]);

        return new MiddlewareClient($middlewareStack, $this->factory);
    }
}
