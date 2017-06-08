<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\Transport\CurlTransport;

final class ClientFactory
{
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
            new CurlTransport($requestConfig),
            new Deflate(),
        ]);

        return new MiddlewareClient($middlewareStack);
    }
}
