<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\StreamTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class MiddlewareClientTest extends TestCase
{
    public function testResponse()
    {
        $client = new MiddlewareClient([
            new StreamTransport(new RequestConfig()),
        ]);

        $request = new Request(new Uri('https://example.com/'), 'GET');
        $response = $client->sendRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
