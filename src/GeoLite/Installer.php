<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

/**
 * The GeoLite2 installer installs new versions of the GeoLite2 database.
 */
class Installer
{
    /**
     * GeoLite2 instance of the current (old) database that needs to be updated.
     *
     * @var GeoLite
     */
    protected $oldGeoLite;

    /**
     * GeoLite2 instance of the new database, stored in a temporary directory.
     *
     * @var GeoLite
     */
    protected $newGeoLite;

    /**
     * Path of the new database, in a temporary directory.
     *
     * @var string
     */
    protected $newPath;

    /**
     * @param  GeoLite  $geoLite  GeoLite2 instance of the current (old) database that needs to be updated.
     * @param  string  $newPath  Path of the new database, in a temporary directory.
     */
    public function __construct(GeoLite $geoLite, string $newPath)
    {
        $this->oldGeoLite = $geoLite;
        $this->newPath = $newPath;
        $this->newGeoLite = GeoLite::fromPath($this->newPath);
    }

    /**
     * Checks the GeoLite2 database and compares the build epoch.
     *
     * @return bool
     */
    public function isMoreRecent(): bool
    {
        return $this->oldGeoLite->getMetadata()->buildEpoch < $this->newGeoLite->getMetadata()->buildEpoch;
    }

    /**
     * Runs the install process.
     */
    public function install(): void
    {
        $dbPath = $this->oldGeoLite->getDatabasePath();
        $tempPath = $this->newGeoLite->getDatabasePath();
        rename($tempPath, $dbPath);
    }
}
