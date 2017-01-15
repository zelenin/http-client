<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Test\Middleware;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware\UserAgent;
use function Zelenin\HttpClient\version;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

final class UserAgentTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultUserAgent()
    {
        $middleware = new UserAgent();

        $request = new Request();
        $response = new Response();

        $this->assertFalse($request->hasHeader('User-Agent'));

        $middleware($request, $response, function (
            RequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) {
            $this->assertTrue($request->hasHeader('User-Agent'));
            $this->assertEquals(sprintf('HttpClient/%s PHP/%s', version(), PHP_VERSION), $request->getHeader('User-Agent')[0]);

            return $response;
        });
    }

    public function testUserAgent()
    {
        $userAgent = 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0';
        $middleware = new UserAgent($userAgent);

        $request = new Request();
        $response = new Response();

        $middleware($request, $response, function (
            RequestInterface $request,
            ResponseInterface $response,
            callable $next = null
        ) use ($userAgent) {
            $this->assertEquals($userAgent, $request->getHeader('User-Agent')[0]);

            return $response;
        });
    }
}
