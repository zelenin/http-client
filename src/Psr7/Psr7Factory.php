<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Psr7;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

interface Psr7Factory
{
    /**
     * @param StreamInterface $stream
     * @param int $status
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public function createResponse(StreamInterface $stream, int $status, array $headers = []): ResponseInterface;

    /**
     * @param resource $resource
     *
     * @return StreamInterface
     */
    public function createStream($resource): StreamInterface;
}
