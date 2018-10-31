<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Zelenin\HttpClient\FallbackMiddleware;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\RequestHandler;
use Zelenin\HttpClient\RequestHandlerInterface;
use Zelenin\HttpClient\Test\Resource\ClosureWrapMiddleware;
use Zend\Diactoros\Request;
use Zend\Diactoros\ResponseFactory;

final class UserAgentTest extends TestCase
{
    public function testDefaultUserAgent()
    {
        $responseFactory = new ResponseFactory();

        $request = new Request();

        $this->assertFalse($request->hasHeader('User-Agent'));

        $requestHandler = new RequestHandler([
            new UserAgent(),
            new ClosureWrapMiddleware(function (RequestInterface $request, RequestHandlerInterface $handler) {
                $this->assertTrue($request->hasHeader('User-Agent'));
                $this->assertEquals(sprintf('HttpClient PHP/%s', PHP_VERSION), $request->getHeader('User-Agent')[0]);

                return $handler->handle($request);
            }),
        ], new FallbackMiddleware($responseFactory));

        $requestHandler->handle($request);
    }

    public function testUserAgent()
    {
        $responseFactory = new ResponseFactory();

        $userAgent = 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0';

        $request = new Request();

        $this->assertFalse($request->hasHeader('User-Agent'));

        $requestHandler = new RequestHandler([
            new UserAgent($userAgent),
            new ClosureWrapMiddleware(function (RequestInterface $request, RequestHandlerInterface $handler) use ($userAgent) {
                $this->assertTrue($request->hasHeader('User-Agent'));
                $this->assertEquals($userAgent, $request->getHeader('User-Agent')[0]);

                return $handler->handle($request);
            }),
        ], new FallbackMiddleware($responseFactory));

        $requestHandler->handle($request);
    }
}
