<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Exception;

use Psr\Http\Client\NetworkExceptionInterface;

final class NetworkException extends RequestException implements NetworkExceptionInterface
{
}
