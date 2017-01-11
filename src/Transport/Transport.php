<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Transport;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zelenin\HttpClient\RequestConfig;

interface Transport
{
    /**
     * @param RequestInterface $request
     * @param RequestConfig $requestConfig
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request, RequestConfig $requestConfig = null): ResponseInterface;
}
