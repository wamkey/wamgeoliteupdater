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

    /**
     * Metadata from the GitHub API. 'null' if the {@see self::getMetadata()} method has not yet been called.
     *
     * @var array|null
     */
    protected $metadata;

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
        if(! is_array($this->metadata)) {
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
            $this->metadata = json_decode($response->getContent(), true);
        }

        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function isOutdated(GeoLite $geoLite): bool
    {
        $filePath = $geoLite->getDatabasePath();

        $currentSha1 = self::generateGitSha1($filePath);
        $newSha1 = $this->getMetadata()['sha'];

        return $currentSha1 !== $newSha1;
    }

    /**
     * @inheritDoc
     */
    public function download(): string
    {
        $downloadPath = _PS_ROOT_DIR_ . '/var/cache/GithubFetcher-GeoLite-tmp.mmdb';
        $metadata = $this->getMetadata();

        if(file_exists($downloadPath)) {
            // Check that the file currently stored in the temporary directory is already downloaded, by checking its
            // hash with the one provided in the metadata. If that is the case, return the path immediately.
            if(hash_equals(self::generateGitSha1($downloadPath), $metadata['sha'])) {
                return $downloadPath;
            }
            // Delete temporary file if it is not valid before downloading it from the metadata's download URL.
            else {
                unlink($downloadPath);
            }
        }

        // Download database file.
        $response = $this->getHttpClient()->request('GET', $metadata['download_url']);

        // Write file.
        $fileHandler = fopen($downloadPath, 'w');
        foreach($this->getHttpClient()->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        return $downloadPath;
    }

    /**
     * Git-style SHA1 hash for a specific file with a blob type.
     *
     * See https://stackoverflow.com/a/5290484 for details of SHA-1 generation by a Git client.
     *
     * @param  string  $filePath  Path to the file that needs to be SHA-1'd.
     * @return string SHA-1 hash of the file with the Git prefix.
     */
    public static function generateGitSha1(string $filePath): string
    {
        $filesize = filesize($filePath);
        $prefix = 'blob ' . $filesize . "\0";

        return sha1($prefix . file_get_contents($filePath));
    }
}
