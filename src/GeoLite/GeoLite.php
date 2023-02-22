<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\Metadata;

class GeoLite
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Path to the GeoLite2 database.
     *
     * @var string|null
     */
    protected $dbPath;

    /**
     * @param  Reader  $reader  Database reader instance.
     */
    protected function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Create an instance using a database provided by its path.
     *
     * @param  string  $dbPath
     * @param  array  $locales
     * @return static
     */
    public static function fromPath(string $dbPath, array $locales = ['en']): self
    {
        $instance = new self(new Reader($dbPath, $locales));
        $instance->setPath($dbPath);

        return $instance;
    }

    /**
     * Sets a new database path on instantiation.
     *
     * @param  string  $dbPath
     */
    protected function setPath(string $dbPath): void
    {
        $this->dbPath = $dbPath;
    }

    /**
     * Returns metadata fetched from the database.
     *
     * @return Metadata
     */
    public function getMetadata(): Metadata
    {
        return $this->reader->metadata();
    }

    /**
     * Path to the GeoLite2 database
     *
     * @return string
     */
    public function getDatabasePath(): string
    {
        return $this->dbPath;
    }

    /**
     * Returns the database build version.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->getMetadata()->binaryFormatMajorVersion . '.' . $this->getMetadata()->binaryFormatMinorVersion;
    }

    /**
     * Returns the database update date.
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return new \DateTime(date('Y-m-d H:i:s', $this->getMetadata()->buildEpoch));
    }

    /**
     * Returns a formatted limited set of properties from the database metadata.
     *
     * @return array
     */
    public function getFormattedMetadata(): array
    {
        return [
            'dbPath' => $this->getDatabasePath(),
            'version' => $this->getVersion(),
            'date' => $this->getDate()->format('Y-m-d'),
            'epoch' => $this->getMetadata()->buildEpoch,
        ];
    }
}
