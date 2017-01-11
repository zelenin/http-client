# HTTP client

[PSR-7](http://www.php-fig.org/psr/psr-7/) compatible HTTP client with middleware support.

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require zelenin/http-client "~0.0.1"
```

or add

```
"zelenin/http-client": "~0.0.1"
```

to the ```require``` section of your ```composer.json```

## Usage

```php
use Zelenin\HttpClient\ClientFactory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

$client = (new ClientFactory())->createCurlClient();

$request = new Request(new Uri('https://example.com/'), 'GET');
$response = $client->send($request);
```

### Full example with middleware stack

```php
use Zelenin\HttpClient\Middleware\Cookie\CookieRequest;
use Zelenin\HttpClient\Middleware\Cookie\CookieResponse;
use Zelenin\HttpClient\Middleware\Cookie\FileStorage;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\MiddlewareStack;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

$requestConfig = new RequestConfig();

$cookieStorage = new FileStorage('/tmp/http-client/cookies.storage');

/**
 * Middlewares order is important.
 */
$middlewareStack = new MiddlewareStack([
    new CookieRequest($cookieStorage), // pre-request middleware
    new UserAgent(sprintf('HttpClient/0.0.1 PHP/%s', PHP_VERSION)), // pre-request middleware
    new CurlTransport($requestConfig), // request middleware
    new Deflate(), // post-request middleware
    new CookieResponse($cookieStorage) // post-request middleware
]);

$client = new MiddlewareClient($middlewareStack);

$request = new Request(new Uri('https://example.com/'), 'GET');
$response = $client->send($request);
```

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
