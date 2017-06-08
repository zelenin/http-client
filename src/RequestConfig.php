<?php
declare(strict_types=1);

namespace Zelenin\HttpClient;

final class RequestConfig
{
    /**
     * @var bool
     */
    private $followLocation;

    /**
     * @var float
     */
    private $timeout;

    public function __construct()
    {
        $this->followLocation = true;
        $this->timeout = 10.0;
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
        return $this->timeout;
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
