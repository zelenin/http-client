<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware\Cookie;

use Dflydev\FigCookies\SetCookies;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware;
use Zelenin\HttpClient\MiddlewareDispatcher;

final class CookieResponse implements Middleware
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, MiddlewareDispatcher $dispatcher): ResponseInterface
    {
        $response = $dispatcher->response();

        foreach (SetCookies::fromResponse($response)->getAll() as $setCookie) {
            $this->storage->add($setCookie);
        }

        return $dispatcher($request);
    }
}
