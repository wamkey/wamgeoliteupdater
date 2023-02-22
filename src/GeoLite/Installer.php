<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

class Installer
{
    /**
     * @var GeoLite
     */
    protected $oldGeoLite;

    /**
     * @var GeoLite
     */
    protected $newGeoLite;

    /**
     * @var string
     */
    protected $newPath;

    public function __construct(GeoLite $geoLite, string $newPath)
    {
        $this->oldGeoLite = $geoLite;
        $this->newPath = $newPath;
        $this->newGeoLite = GeoLite::fromPath($this->newPath);
    }

    public function isMoreRecent(): bool
    {
        return $this->oldGeoLite->getMetadata()->buildEpoch < $this->newGeoLite->getMetadata()->buildEpoch;
    }

    public function install(): void
    {
        $dbPath = $this->oldGeoLite->getDatabasePath();
        $tempPath = $this->newGeoLite->getDatabasePath();
        rename($tempPath, $dbPath);
    }
}
