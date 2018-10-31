<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\FallbackMiddleware;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\RequestHandler;
use Zelenin\HttpClient\Transport\StreamTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\Uri;

final class DeflateTest extends TestCase
{
    public function testDeflate()
    {
        $streamFactory = new StreamFactory();
        $responseFactory = new ResponseFactory();

        $requestHandler = new RequestHandler([
            new Deflate($streamFactory),
            new StreamTransport($streamFactory, $responseFactory, new RequestConfig()),
        ], new FallbackMiddleware($responseFactory));

        $request = new Request(new Uri('https://example.com/'), 'GET', 'php://temp', [
            'Accept-Encoding' => 'gzip',
        ]);

        $response = $requestHandler->handle($request);

        $this->assertFalse($response->hasHeader('Content-Encoding'));
        $this->assertEquals('gzip', $response->getHeader('Content-Encoding-Original')[0]);
        $this->assertContains('Example Domain', $response->getBody()->getContents());
    }
}
