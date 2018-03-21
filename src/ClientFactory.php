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

        return new MiddlewareClient([
            new UserAgent(),
            new Deflate(),
            new CurlTransport($requestConfig),
        ]);
    }
}
