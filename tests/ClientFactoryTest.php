<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\Client;
use Zelenin\HttpClient\ClientFactory;

final class ClientFactoryTest extends TestCase
{
    public function testClient()
    {
        $factory = new ClientFactory();

        $this->assertInstanceOf(Client::class, $factory->create());
    }
}
