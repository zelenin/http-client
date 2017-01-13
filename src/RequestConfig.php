<?php
declare(strict_types = 1);

namespace Zelenin\HttpClient;

final class RequestConfig
{
    const ATTRIBUTE_NAME = 'requestConfig';

    /**
     * @var bool
     */
    private $followLocation;

    /**
     * @var float
     */
    private $timeOut;

    public function __construct()
    {
        $this->followLocation = true;
        $this->timeOut = 10.0;
    }

    /**
     * @return bool
     */
    public function followLocation(): bool
    {
        return $this->followLocation;
    }

    /**
     * @param bool $followLocation
     *
     * @return RequestConfig
     */
    public function setFollowLocation(bool $followLocation): self
    {
        $this->followLocation = $followLocation;

        return $this;
    }

    /**
     * @return float
     */
    public function timeout(): float
    {
        return $this->timeOut;
    }

    /**
     * @param float $timeout
     *
     * @return RequestConfig
     */
    public function setTimeout(float $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }
}
