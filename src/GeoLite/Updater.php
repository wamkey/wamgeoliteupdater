<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use Wamkey\GeoLiteUpdater\GeoLite\Contracts\FetcherInterface;

class Updater
{
    /**
     * @var FetcherInterface
     */
    protected $fetcher;

    public function __construct(FetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function update(): void
    {
        //
    }
}
