<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Zelenin\HttpClient\FinalMiddleware;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\MiddlewareDispatcher;
use Zelenin\HttpClient\MiddlewareStack;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Test\Resource\ClosureWrapMiddleware;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

final class DeflateTest extends TestCase
{
    public function testDeflate()
    {
        $dispatcher = new MiddlewareDispatcher(new MiddlewareStack([
            new CurlTransport(new RequestConfig()),
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) {
                $response = $dispatcher->response();

                $this->assertEquals('gzip', $response->getHeader('Content-Encoding')[0]);
                $this->assertNotContains('Example Domain', $response->getBody()->getContents());

                return $dispatcher($request);
            }, true),
            new Deflate(),
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) {
                $dispatcher($request);
                $response = $dispatcher->response();

                $this->assertContains('Example Domain', $response->getBody()->getContents());

                return $response;
            }, true),
        ]), new FinalMiddleware());

        $request = new Request(new Uri('https://example.com/'), 'GET', 'php://temp', [
            'Accept-Encoding' => 'gzip',
        ]);

        $dispatcher($request);
    }
}
