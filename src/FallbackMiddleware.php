<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

final class FallbackMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->responseFactory->createResponse();
    }
}
