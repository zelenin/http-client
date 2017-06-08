<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Exception;

use RuntimeException;

class RequestException extends RuntimeException implements HttpClientException
{
}
