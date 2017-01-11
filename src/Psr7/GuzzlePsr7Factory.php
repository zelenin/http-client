<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Psr7;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class GuzzlePsr7Factory implements Psr7Factory
{
    /**
     * @inheritdoc
     */
    public function createResponse(StreamInterface $stream, int $status, array $headers = []): ResponseInterface
    {
        return new Response($status, $headers, $stream);
    }

    /**
     * @inheritdoc
     */
    public function createStream($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
