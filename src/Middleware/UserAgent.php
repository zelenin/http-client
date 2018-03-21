<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestHandlerInterface;

final class UserAgent implements MiddlewareInterface
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
        $this->userAgent = $userAgent ?: sprintf('HttpClient PHP/%s', PHP_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('User-Agent')) {
            $request = $request->withHeader('User-Agent', $this->userAgent);
        }

        return $handler->handle($request);
    }
}
