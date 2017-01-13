<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware;

final class UserAgent implements Middleware
{
    /**
     * @var string
     */
    private $userAgent;

    /**
     * @param string $userAgent
     */
    public function __construct(string $userAgent = null)
    {
        $this->userAgent = $userAgent ?: sprintf('HttpClient/0.0.5 PHP/%s', PHP_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if (!$request->hasHeader('User-Agent')) {
            $request = $request->withHeader('User-Agent', $this->userAgent);
        }

        return $next($request, $response);
    }
}
