<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Exception\ConnectException;
use Zelenin\HttpClient\Exception\RequestException;
use Zelenin\HttpClient\Middleware;
use Zelenin\HttpClient\Psr7\Psr7Factory;
use Zelenin\HttpClient\RequestConfig;
use function Zelenin\HttpClient\copyResourceToStream;
use function Zelenin\HttpClient\deserializeHeadersToPsr7Format;
use function Zelenin\HttpClient\serializeHeadersFromPsr7Format;

final class StreamTransport implements Transport, Middleware
{
    /**
     * @var RequestConfig
     */
    private $requestConfig;

    /**
     * @var Psr7Factory
     */
    private $factory;

    /**
     * @param RequestConfig $requestConfig
     */
    public function __construct(RequestConfig $requestConfig, Psr7Factory $factory)
    {
        $this->requestConfig = $requestConfig;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request, RequestConfig $requestConfig = null): ResponseInterface
    {
        $requestConfig = $requestConfig ?: $this->requestConfig;

        $context = [
            'http' => [
                'method' => $request->getMethod(),
                'header' => serializeHeadersFromPsr7Format($request->getHeaders()),
                'protocol_version' => $request->getProtocolVersion(),
                'ignore_errors' => true,
                'timeout' => $requestConfig->timeout(),
                'follow_location' => $requestConfig->followLocation()
            ],
        ];

        if ($request->getBody()->getSize()) {
            $context['http']['content'] = $request->getBody()->__toString();
        }

        $resource = fopen($request->getUri()->__toString(), 'rb', false, stream_context_create($context));

        if (!is_resource($resource)) {
            $error = error_get_last()['message'];
            if (strpos($error, 'getaddrinfo') || strpos($error, 'Connection refused')) {
                $e = new ConnectException($error, 0);
            } else {
                $e = new RequestException($error, 0);
            }
            throw $e;
        }

        $stream = copyResourceToStream($resource, $this->factory);

        $headers = stream_get_meta_data($resource)['wrapper_data'] ?? [];

        fclose($resource);

        $parts = explode(' ', array_shift($headers), 3);
        $version = explode('/', $parts[0])[1];
        $status = (int)$parts[1];

        $response = $this->factory
            ->createResponse($stream, $status, deserializeHeadersToPsr7Format($headers))
            ->withProtocolVersion($version);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        RequestConfig $requestConfig,
        callable $next
    ): ResponseInterface {
        return $next($request, $this->send($request, $requestConfig), $requestConfig);
    }
}
