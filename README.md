# HTTP client [![Build Status](https://travis-ci.org/zelenin/http-client.svg?branch=master)](https://travis-ci.org/zelenin/http-client) [![Coverage Status](https://coveralls.io/repos/github/zelenin/http-client/badge.svg?branch=master)](https://coveralls.io/github/zelenin/http-client?branch=master)

[PSR-7](http://www.php-fig.org/psr/psr-7/) compatible HTTP client with middleware support.

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require zelenin/http-client "~1.0.0"
```

or add

```
"zelenin/http-client": "~1.0.0"
```

to the ```require``` section of your ```composer.json```

## Usage

```php
use Zelenin\HttpClient\ClientFactory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

$client = (new ClientFactory())->create();

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
use Zelenin\HttpClient\Psr7\DiactorosPsr7Factory;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\CurlTransport;
use function Zelenin\HttpClient\version;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

$cookieStorage = new FileStorage('/tmp/http-client/cookies.storage');

$middlewareStack = new MiddlewareStack([
    new CookieRequest($cookieStorage), // pre-request middleware
    new UserAgent(sprintf('HttpClient/%s PHP/%s', version(), PHP_VERSION)), // pre-request middleware
    new CurlTransport(new RequestConfig()), // request middleware
    new Deflate(),  // post-request middleware
    new CookieResponse($cookieStorage) // post-request middleware
]);

$client = new MiddlewareClient($middlewareStack, $psr7Factory);

$request = new Request(new Uri('https://example.com/'), 'GET');
$response = $client->send($request);
```

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
