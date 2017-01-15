<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Zelenin\HttpClient\Psr7\Psr7Factory;

/**
 * @param array $headers
 *
 * @return string
 */
function serializeHeadersFromPsr7Format(array $headers): string
{
    $lines = [];
    foreach ($headers as $header => $values) {
        $normalized = normalizeHeader($header);
        foreach ($values as $value) {
            $lines[] = sprintf('%s: %s', $normalized, $value);
        }
    }

    return implode("\r\n", $lines);
}

/**
 * @param array $lines
 *
 * @return array
 */
function deserializeHeadersToPsr7Format(array $lines): array
{
    $headers = [];

    foreach ($lines as $line) {
        $parts = explode(':', $line, 2);
        $headers[trim($parts[0])][] = trim($parts[1] ?? null);
    }

    return $headers;
}

/**
 * @param string $header
 *
 * @return string
 */
function normalizeHeader(string $header): string
{
    $header = str_replace('-', ' ', $header);
    $filtered = ucwords($header);

    return str_replace(' ', '-', $filtered);
}

/**
 * @param StreamInterface $stream
 *
 * @return resource
 */
function copyStreamToResource(StreamInterface $stream)
{
    $resource = fopen('php://temp', 'rb+');

    $stream->rewind();

    while (!$stream->eof()) {
        fwrite($resource, $stream->read(1048576));
    }

    fseek($resource, 0);

    return $resource;
}

/**
 * @param $resource
 * @param Psr7Factory $factory
 *
 * @return StreamInterface
 */
function copyResourceToStream($resource, Psr7Factory $factory): StreamInterface
{
    if (!is_resource($resource)) {
        throw new InvalidArgumentException('Not resource.');
    }

    $tempResource = fopen('php://temp', 'rb+');

    stream_copy_to_stream($resource, $tempResource);

    $stream = $factory->createStream($tempResource);
    $stream->rewind();

    return $stream;
}

/**
 * @param StreamInterface $stream
 *
 * @return StreamInterface
 */
function inflateStream(StreamInterface $stream, Psr7Factory $factory): StreamInterface
{
    $stream->rewind();

    $stream->read(10);

    $resource = fopen('php://temp', 'rb+');

    while (!$stream->eof()) {
        fwrite($resource, $stream->read(1048576));
    }
    fseek($resource, 0);

    stream_filter_append($resource, "zlib.inflate", STREAM_FILTER_READ);

    return copyResourceToStream($resource, $factory);
}

/**
 * @return string
 */
function version(): string
{
    return '0.1.0';
}
