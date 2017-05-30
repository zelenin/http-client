<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\MiddlewareStack;
use Zelenin\HttpClient\Psr7\DiactorosPsr7Factory;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class MiddlewareClientTest extends TestCase
{
    public function testResponse()
    {
        $requestConfig = new RequestConfig();

        $psr7Factory = new DiactorosPsr7Factory();

        $middlewareStack = new MiddlewareStack([
            function ($request, $response, $next) {
                return $next($request, $response);
            },
            new CurlTransport($requestConfig, $psr7Factory),
            function ($request, $response, $next) {
                return $next($request, $response);
            },
        ]);

        $client = new MiddlewareClient($middlewareStack, $psr7Factory);

        $request = new Request(new Uri('https://example.com/'), 'GET');
        $response = $client->send($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
