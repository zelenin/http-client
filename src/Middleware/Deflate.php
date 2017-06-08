<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\Middleware;
use Zelenin\HttpClient\MiddlewareDispatcher;
use function Zelenin\HttpClient\inflateStream;

final class Deflate implements Middleware
{
    /**
     * @inheritdoc
     */
    public function __invoke(RequestInterface $request, MiddlewareDispatcher $dispatcher): ResponseInterface
    {
        $response = $dispatcher->response();

        if ($response->hasHeader('Content-Encoding')) {
            $encoding = $response->getHeader('Content-Encoding');
            if ($encoding[0] === 'gzip' || $encoding[0] === 'deflate') {
                $stream = inflateStream($response->getBody());

                $response = $response
                    ->withBody($stream)
                    ->withHeader('Content-Encoding-Original', $encoding)
                    ->withoutHeader('Content-Encoding');

                if ($response->hasHeader('Content-Length')) {
                    $response = $response
                        ->withHeader('Content-Length-Original', $response->getHeader('Content-Length')[0])
                        ->withHeader('Content-Length', (string)$response->getBody()->getSize());
                }
            }
        }

        $dispatcher = $dispatcher->withResponse($response);

        return $dispatcher($request);
    }
}
