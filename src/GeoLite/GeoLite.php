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
     * @var string|null
     */
    protected $dbPath;

    protected function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public static function fromPath(string $dbPath, array $locales = ['en']): self
    {
        $instance = new self(new Reader($dbPath, $locales));
        $instance->setPath($dbPath);

        return $instance;
    }

    protected function setPath(string $dbPath): void
    {
        $this->dbPath = $dbPath;
    }

    public function getMetadata(): Metadata
    {
        return $this->reader->metadata();
    }

    public function getDatabasePath(): string
    {
        return $this->dbPath;
    }

    public function getVersion(): string
    {
        return $this->getMetadata()->binaryFormatMajorVersion . '.' . $this->getMetadata()->binaryFormatMinorVersion;
    }

    public function getDate(): \DateTime
    {
        return new \DateTime(date('Y-m-d H:i:s', $this->getMetadata()->buildEpoch));
    }

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
