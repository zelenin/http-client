<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Transport;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class CurlTransportTest extends TestCase
{
    public function testTransport()
    {
        if (PHP_VERSION >= 7) {
            $this->markTestSkipped('Not supported on PHP 7 (empty chunk will not be emitted)');
        }

        $transport = new CurlTransport(new RequestConfig());

        $request = (new Request(new Uri('https://example.org/'), 'GET'))
            ->withHeader('Accept-Encoding', 'text/html');

        $response = $transport->send($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['text/html'], $response->getHeader('Content-type'));
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
