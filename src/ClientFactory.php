<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Client\ClientInterface;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\Transport\CurlTransport;

final class ClientFactory
{
    /**
     * @param RequestConfig|null $requestConfig
     *
     * @return ClientInterface
     */
    public function create(RequestConfig $requestConfig = null): ClientInterface
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
