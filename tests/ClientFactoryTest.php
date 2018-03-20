<?php
declare(strict_types=1);

namespace Zelenin\HttpClient\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Zelenin\HttpClient\ClientFactory;

final class ClientFactoryTest extends TestCase
{
    public function testClient()
    {
        $factory = new ClientFactory();

        $this->assertInstanceOf(ClientInterface::class, $factory->create());
    }
}
