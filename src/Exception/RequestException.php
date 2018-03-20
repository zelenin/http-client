<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Exception;

use Exception;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

class RequestException extends RuntimeException implements \Psr\Http\Client\Exception\RequestException
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param $message
     * @param RequestInterface $request
     *
     * @param Exception|null $previous
     */
    public function __construct($message, RequestInterface $request, Exception $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, 0, $previous);
    }

    /**
     * @inheritdoc
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
