<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware\Cookie;

use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestHandlerInterface;

final class CookieRequest implements MiddlewareInterface
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
        if (!$request->hasHeader('Cookie')) {
            $cookies = [];

            $setCookies = $this->storage->getAll();

            foreach ($setCookies as $setCookie) {
                if ($this->validForUri($setCookie, $request->getUri())) {
                    $cookies[] = sprintf('%s=%s', $setCookie->getName(), $setCookie->getValue());
                }
            }

            if ($cookies) {
                $request = $request->withHeader('Cookie', implode('; ', $cookies));
            }
        }

        return $handler->handle($request);
    }

    /**
     * @param string $cookieDomain
     * @param string $requestDomain
     *
     * @return bool
     */
    public function matchesDomain(string $cookieDomain, string $requestDomain): bool
    {
        $cookieDomain = ltrim($cookieDomain, '.');

        if (!$cookieDomain || !strcasecmp($requestDomain, $cookieDomain)) {
            return true;
        }

        if (filter_var($requestDomain, FILTER_VALIDATE_IP)) {
            return false;
        }

        return (bool)preg_match('/\.' . preg_quote($cookieDomain, '/') . '$/', $requestDomain);
    }

    /**
     * @param SetCookie $setCookie
     * @param UriInterface $uri
     *
     * @return bool
     */
    private function validForUri(SetCookie $setCookie, UriInterface $uri): bool
    {
        $requestPath = $uri->getPath() ?: '/';

        if ($setCookie->getSecure() && $uri->getScheme() !== 'https') {
            return false;
        }

        if ($setCookie->getExpires() && time() > $setCookie->getExpires()) {
            return false;
        }

        if (!$this->matchesPath($setCookie->getPath(), $requestPath)) {
            return false;
        }

        if (!$this->matchesDomain($setCookie->getDomain(), $uri->getHost())) {
            return false;
        }

        return true;
    }

    /**
     * @param string $cookiePath
     * @param string $requestPath
     *
     * @return bool
     */
    private function matchesPath(string $cookiePath, string $requestPath): bool
    {
        if ($cookiePath === '/' || $cookiePath == $requestPath) {
            return true;
        }

        if (0 !== strpos($requestPath, $cookiePath)) {
            return false;
        }

        if (substr($cookiePath, -1, 1) === '/') {
            return true;
        }

        return substr($requestPath, strlen($cookiePath), 1) === '/';
    }
}
