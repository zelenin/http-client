<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Exception;

final class NetworkException extends RequestException implements \Psr\Http\Client\Exception\NetworkException
{
}
