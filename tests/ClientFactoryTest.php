<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\Client;
use Zelenin\HttpClient\ClientFactory;
use Zelenin\HttpClient\Psr7\DiactorosPsr7Factory;

final class ClientFactoryTest extends TestCase
{
    public function testClient()
    {
        $factory = new ClientFactory(new DiactorosPsr7Factory());

        $this->assertInstanceOf(Client::class, $factory->create());
    }
}
