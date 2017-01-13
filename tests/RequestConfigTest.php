<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient\Test;

use PHPUnit_Framework_TestCase;
use Zelenin\HttpClient\RequestConfig;

final class RequestConfigTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $provider = new RequestConfig();

        $this->assertEquals(true, $provider->followLocation());
        $this->assertEquals(10.0, $provider->timeout());
    }

    public function testConfig()
    {
        $followLocation = false;
        $timeout = 25.0;

        $provider = (new RequestConfig())
            ->setFollowLocation($followLocation)
            ->setTimeout($timeout);

        $this->assertEquals($followLocation, $provider->followLocation());
        $this->assertEquals($timeout, $provider->timeout());
    }
}
