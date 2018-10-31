# HTTP client [![Build Status](https://travis-ci.org/zelenin/http-client.svg?branch=master)](https://travis-ci.org/zelenin/http-client) [![Coverage Status](https://coveralls.io/repos/github/zelenin/http-client/badge.svg?branch=master)](https://coveralls.io/github/zelenin/http-client?branch=master)

[PSR-18](http://www.php-fig.org/psr/psr-18/) compatible low-level HTTP client with middleware support.

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require zelenin/http-client "~4.0.0"
```

or add

```
"zelenin/http-client": "~4.0.0"
```

to the ```require``` section of your ```composer.json```

## Usage

```php
use Zelenin\HttpClient\ClientFactory;
use Zend\Diactoros\Request;
use Zend\Diactoros\Uri;

$client = (new ClientFactory())->create();

$request = new Request(new Uri('https://example.com/'), 'GET');
$response = $client->sendRequest($request);
```

### Full example with middleware stack

```php
use Zelenin\HttpClient\Middleware\Cookie\CookieRequest;
use Zelenin\HttpClient\Middleware\Cookie\CookieResponse;
use Zelenin\HttpClient\Middleware\Cookie\FileStorage;
use Zelenin\HttpClient\Middleware\Deflate;
use Zelenin\HttpClient\Middleware\UserAgent;
use Zelenin\HttpClient\MiddlewareClient;
use Zelenin\HttpClient\RequestConfig;
use Zelenin\HttpClient\Transport\CurlTransport;
use Zend\Diactoros\Request;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;
use Zend\Diactoros\Uri;

$streamFactory = new StreamFactory();
$responseFactory = new ResponseFactory();

$cookieStorage = new FileStorage('/tmp/http-client/cookies.storage');

$client = new MiddlewareClient([
    new CookieRequest($cookieStorage),
    new UserAgent(sprintf('HttpClient PHP/%s', PHP_VERSION)),
    new Deflate($streamFactory),
    new CookieResponse($cookieStorage),
    new CurlTransport($streamFactory, $responseFactory, new RequestConfig()),
], $responseFactory);

$request = new Request(new Uri('https://example.com/'), 'GET');
$response = $client->sendRequest($request);
```

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
