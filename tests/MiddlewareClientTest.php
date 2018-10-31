<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\StreamTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\Uri;

final class MiddlewareClientTest extends TestCase
{
    public function testResponse()
    {
        $streamFactory = new StreamFactory();
        $responseFactory = new ResponseFactory();

        $client = new MiddlewareClient([
            new StreamTransport($streamFactory, $responseFactory, new RequestConfig()),
        ], $responseFactory);

        $request = new Request(new Uri('https://example.com/'), 'GET');
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html; charset=UTF-8'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
