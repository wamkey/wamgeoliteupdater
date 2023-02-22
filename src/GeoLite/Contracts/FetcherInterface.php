<?php

namespace Wamkey\GeoLiteUpdater\GeoLite\Contracts;

use Wamkey\GeoLiteUpdater\GeoLite\GeoLite;

interface FetcherInterface
{
    /**
     * Returns information about the new GeoLite2-City file.
     *
     * @return array
     */
    public function getMetadata(): array;

    /**
     * Check if the current GeoLite2-City file is the latest.
     *
     * @param  GeoLite  $geoLite
     * @return bool 'true' if there is a new version of the file; 'false' otherwise.
     */
    public function isOutdated(GeoLite $geoLite): bool;

    /**
     * Download new GeoLite2-City file.
     *
     * @return string Path of the downloaded file.
     */
    public function download(): string;
}
