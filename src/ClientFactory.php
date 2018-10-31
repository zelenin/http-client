<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Client\ClientInterface;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;

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
        $streamFactory = new StreamFactory();
        $responseFactory = new ResponseFactory();

        return new MiddlewareClient([
            new UserAgent(),
            new Deflate($streamFactory),
            new CurlTransport($streamFactory, $responseFactory, $requestConfig),
        ], $responseFactory);
    }
}
