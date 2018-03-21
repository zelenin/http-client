<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Exception\ConnectException;
use Zelenin\HttpClient\Exception\NetworkException;
use Zelenin\HttpClient\Exception\RequestException;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\RequestHandlerInterface;
use Zend\Diactoros\Response;
use function Zelenin\HttpClient\copyResourceToStream;
use function Zelenin\HttpClient\deserializeHeadersToPsr7Format;
use function Zelenin\HttpClient\filterLastResponseHeaders;
use function Zelenin\HttpClient\serializeHeadersFromPsr7Format;

final class StreamTransport implements Transport, MiddlewareInterface
{
    /**
     * @var RequestConfig
     */
    private $requestConfig;

    /**
     * @param RequestConfig $requestConfig
     */
    public function __construct(RequestConfig $requestConfig)
    {
        $this->requestConfig = $requestConfig;
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $context = [
            'http' => [
                'method' => $request->getMethod(),
                'header' => serializeHeadersFromPsr7Format($request->getHeaders()),
                'protocol_version' => $request->getProtocolVersion(),
                'ignore_errors' => true,
                'timeout' => $this->requestConfig->timeout(),
                'follow_location' => $this->requestConfig->followLocation(),
            ],
        ];

        if ($request->getBody()->getSize()) {
            $context['http']['content'] = $request->getBody()->__toString();
        }

        $resource = fopen($request->getUri()->__toString(), 'rb', false, stream_context_create($context));

        if (!is_resource($resource)) {
            $error = error_get_last()['message'];
            if (strpos($error, 'getaddrinfo') || strpos($error, 'Connection refused')) {
                $e = new NetworkException($error, $request);
            } else {
                $e = new RequestException($error, $request);
            }
            throw $e;
        }

        $stream = copyResourceToStream($resource);

        $headers = stream_get_meta_data($resource)['wrapper_data'] ?? [];

        if ($this->requestConfig->followLocation()) {
            $headers = filterLastResponseHeaders($headers);
        }

        fclose($resource);

        $parts = explode(' ', array_shift($headers), 3);
        $version = explode('/', $parts[0])[1];
        $status = (int)$parts[1];

        $response = (new Response($stream, $status, deserializeHeadersToPsr7Format($headers)))
            ->withProtocolVersion($version);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->sendRequest($request);
    }
}
