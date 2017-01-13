<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Transport
{
    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request): ResponseInterface;
}
