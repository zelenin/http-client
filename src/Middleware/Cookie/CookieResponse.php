<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware\Cookie;

use Dflydev\FigCookies\SetCookies;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestHandlerInterface;

final class CookieResponse implements MiddlewareInterface
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
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        foreach (SetCookies::fromResponse($response)->getAll() as $setCookie) {
            $this->storage->add($setCookie);
        }

        return $response;
    }
}
