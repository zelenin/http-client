<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\MiddlewareDispatcher;
use Zelenin\HttpClient\MiddlewareStack;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Test\Resource\ClosureWrapMiddleware;
use Zelenin\HttpClient\Transport\StreamTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class MiddlewareClientTest extends TestCase
{
    public function testResponse()
    {
        $requestConfig = new RequestConfig();

        $middlewareStack = new MiddlewareStack([
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) {
                return $dispatcher($request);
            }, true),
            new StreamTransport($requestConfig),
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) {
                return $dispatcher($request);
            }, true),
        ]);

        $client = new MiddlewareClient($middlewareStack);

        $request = new Request(new Uri('https://example.com/'), 'GET');
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
