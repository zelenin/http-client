<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware;
use Zelenin\HttpClient\MiddlewareDispatcher;
use function Zelenin\HttpClient\version;

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
        $this->userAgent = $userAgent ?: sprintf('HttpClient/%s PHP/%s', version(), PHP_VERSION);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, MiddlewareDispatcher $dispatcher): ResponseInterface
    {
        if (!$request->hasHeader('User-Agent')) {
            $request = $request->withHeader('User-Agent', $this->userAgent);
        }

        return $dispatcher($request);
    }
}
