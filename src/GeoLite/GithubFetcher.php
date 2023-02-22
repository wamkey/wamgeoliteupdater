<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * This implementation of a database fetcher downloads updates from GitHub.
 */
class GithubFetcher implements FetcherInterface
{
    /**
     * Repository name (e.g. vendor/repo).
     *
     * @var string
     */
    protected $repositoryName;

    /**
     * Repository context (branch name e.g. 'master' or commit hash).
     *
     * @var string
     */
    protected $repositoryContext;

    /**
     * Path to file in the current repository.
     *
     * @var string
     */
    protected $repositoryPath;

    public function __construct()
    {
        $this->repositoryName = 'P3TERX/GeoLite.mmdb';
        $this->repositoryContext = 'download';
        $this->repositoryPath = 'GeoLite2-City.mmdb';
    }

    /**
     * Fetch a new {@see HttpClientInterface} implementation.
     *
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface
    {
        static $_instance;

        if($_instance === null) {
            $_instance = HttpClient::create();
        }

        return $_instance;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(): array
    {
        static $_cache;

        if($_cache === null) {
            $response = $this->getHttpClient()->request(
                'GET',
                'https://api.github.com/repos/' . $this->repositoryName
                . '/contents/' . $this->repositoryPath . '?ref=' . $this->repositoryContext,
                [
                    'headers' => [
                        'Connection' => 'keep-alive',
                        'X-GitHub-Api-Version' => '2022-11-28',
                        'Accept' => 'application/vnd.github+json',
                    ]
                ]
            );
            $_cache = json_decode($response->getContent(), true);
        }

        return $_cache;
    }

    /**
     * @inheritDoc
     */
    public function isOutdated(GeoLite $geoLite): bool
    {
        $filePath = $geoLite->getDatabasePath();

        $filesize = filesize($filePath);
        $prefix = 'blob ' . $filesize . "\0"; // Required to calculate SHA1, see: https://stackoverflow.com/a/5290484

        $currentSha1 = sha1($prefix . file_get_contents($filePath));
        $newSha1 = $this->getMetadata()['sha'];

        return $currentSha1 !== $newSha1;
    }

    /**
     * @inheritDoc
     */
    public function download(): string
    {
        $downloadPath = _PS_ROOT_DIR_ . '/var/cache/tempgeolite.mmdb';
        $metadata = $this->getMetadata();

        if(file_exists($downloadPath) && sha1_file($downloadPath) === $metadata['sha']) {
            return $downloadPath;
        }

        $response = $this->getHttpClient()->request('GET', $metadata['download_url']);

        if(file_exists($downloadPath)) {
            unlink($downloadPath);
        }

        $fileHandler = fopen($downloadPath, 'w');
        foreach($this->getHttpClient()->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        return $downloadPath;
    }
}
