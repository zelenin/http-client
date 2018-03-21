<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\MiddlewareInterface;
use Zelenin\HttpClient\RequestHandlerInterface;
use function Zelenin\HttpClient\inflateStream;

final class Deflate implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

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

        return $response;
    }
}
