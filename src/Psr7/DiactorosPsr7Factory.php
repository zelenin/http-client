<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Psr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

final class DiactorosPsr7Factory implements Psr7Factory
{
    /**
     * @inheritdoc
     */
    public function createResponse(StreamInterface $stream, int $status, array $headers = []): ResponseInterface
    {
        return new Response($stream, $status, $headers);
    }

    /**
     * @inheritdoc
     */
    public function createStream($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
