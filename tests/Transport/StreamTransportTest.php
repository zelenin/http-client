<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Transport;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\Psr7\DiactorosPsr7Factory;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\StreamTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class StreamTransportTest extends TestCase
{
    public function testTransport()
    {
        $transport = new StreamTransport(new RequestConfig(), new DiactorosPsr7Factory());

        $request = (new Request(new Uri('https://example.org/'), 'GET'))
            ->withHeader('Accept-Encoding', 'text/html');

        $response = $transport->send($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
