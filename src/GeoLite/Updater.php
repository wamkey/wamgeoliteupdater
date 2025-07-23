<?php

declare(strict_types=1);

namespace Wamkey\GeoLiteUpdater\GeoLite;

/**
 * The GeoLite2 updater downloads and checks for updates for a GeoLite2 instance.
 */
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

    /**
     * @param  GeoLite  $geoLite
     * @param  FetcherInterface  $fetcher
     */
    public function __construct(GeoLite $geoLite, FetcherInterface $fetcher)
    {
        $this->geoLite = $geoLite;
        $this->fetcher = $fetcher;
    }

    /**
     * Checks if there is a new update available.
     *
     * @return bool
     */
    public function isOutdated(): bool
    {
        return $this->fetcher->isOutdated($this->geoLite);
    }

    /**
     * Downloads the database update and saves it in a temporary directory.
     *
     * @return string Path of the database downloaded in a temporary directory.
     */
    public function update(): string
    {
        if(! $this->isOutdated()) {
            throw new \InvalidArgumentException("Current database is up-to-date.");
        }

        return $this->fetcher->download();
    }
}
