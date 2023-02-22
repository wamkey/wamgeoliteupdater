<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use Wamkey\GeoLiteUpdater\GeoLite\Contracts\FetcherInterface;

class Updater
{
    /**
     * @var GeoLite
     */
    protected $geoLite;

    /**
     * @var FetcherInterface
     */
    protected $fetcher;

    public function __construct(GeoLite $geoLite, FetcherInterface $fetcher)
    {
        $this->geoLite = $geoLite;
        $this->fetcher = $fetcher;
    }

    public function isOutdated(): bool
    {
        return $this->fetcher->isOutdated($this->geoLite);
    }

    public function update(): string
    {
        if(! $this->isOutdated()) {
            throw new \InvalidArgumentException("Current database is up-to-date.");
        }

        return $this->fetcher->download();
    }
}
