<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Exception\ConnectException;
use Zelenin\HttpClient\Exception\NetworkException;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\RequestHandlerInterface;
use Zend\Diactoros\Response;
use function Zelenin\HttpClient\copyResourceToStream;
use function Zelenin\HttpClient\deserializeHeadersToPsr7Format;
use function Zelenin\HttpClient\filterLastResponseHeaders;
use function Zelenin\HttpClient\serializeHeadersFromPsr7Format;

final class CurlTransport implements Transport, MiddlewareInterface
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
        $resource = fopen('php://temp', 'wb');

        $curlOptions = [
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => $this->requestConfig->followLocation(),
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => $this->requestConfig->timeout(),
            CURLOPT_FILE => $resource,
        ];

        $version = $request->getProtocolVersion();
        switch ($version) {
            case "1.0":
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
                break;

            case "1.1":
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;

            case "2.0":
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
                break;

            default:
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;
        }

        $curlOptions[CURLOPT_HTTPHEADER] = explode("\r\n", serializeHeadersFromPsr7Format($request->getHeaders()));

        if ($request->getBody()->getSize()) {
            $curlOptions[CURLOPT_POSTFIELDS] = $request->getBody()->__toString();
        }

        $headers = [];
        $curlOptions[CURLOPT_HEADERFUNCTION] = function ($resource, $headerString) use (&$headers) {
            $header = trim($headerString);
            if (strlen($header) > 0) {
                $headers[] = $header;
            }

            return mb_strlen($headerString, '8bit');
        };

        $curlResource = curl_init($request->getUri()->__toString());
        curl_setopt_array($curlResource, $curlOptions);

        curl_exec($curlResource);

        $stream = copyResourceToStream($resource);

        if ($this->requestConfig->followLocation()) {
            $headers = filterLastResponseHeaders($headers);
        }

        fclose($resource);

        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        if ($errorNumber) {
            throw new NetworkException($errorMessage, $request);
        }

        $parts = explode(' ', array_shift($headers), 3);
        $version = explode('/', $parts[0])[1];
        $status = (int)$parts[1];

        curl_close($curlResource);

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
