<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Zelenin\HttpClient\FinalMiddleware;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\MiddlewareDispatcher;
use Zelenin\HttpClient\MiddlewareStack;
use Zelenin\HttpClient\Test\Resource\ClosureWrapMiddleware;
use Zend\Diactoros\Request;

final class UserAgentTest extends TestCase
{
    public function testDefaultUserAgent()
    {
        $middleware = new UserAgent();

        $request = new Request();

        $this->assertFalse($request->hasHeader('User-Agent'));

        $dispatcher = new MiddlewareDispatcher(new MiddlewareStack([
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) {
                $this->assertTrue($request->hasHeader('User-Agent'));
                $this->assertEquals(sprintf('HttpClient PHP/%s', PHP_VERSION), $request->getHeader('User-Agent')[0]);

                return $dispatcher($request);
            }, true),
        ]), new FinalMiddleware());

        $middleware($request, $dispatcher);
    }

    public function testUserAgent()
    {
        $userAgent = 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0';
        $middleware = new UserAgent($userAgent);

        $request = new Request();

        $dispatcher = new MiddlewareDispatcher(new MiddlewareStack([
            new ClosureWrapMiddleware(function (RequestInterface $request, MiddlewareDispatcher $dispatcher) use ($userAgent) {
                $this->assertEquals($userAgent, $request->getHeader('User-Agent')[0]);

                return $dispatcher($request);
            }, true),
        ]), new FinalMiddleware());

        $middleware($request, $dispatcher);
    }
}
