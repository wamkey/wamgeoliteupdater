<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Wamkey\GeoLiteUpdater\GeoLite\Contracts\FetcherInterface;

class GithubFetcher implements FetcherInterface
{
    protected $repositoryName;

    protected $repositoryContext;

    protected $repositoryPath;

    public function __construct()
    {
        $this->repositoryName = 'P3TERX/GeoLite.mmdb';
        $this->repositoryContext = 'download';
        $this->repositoryPath = 'GeoLite2-City.mmdb';
    }

    public function getHttpClient(): HttpClientInterface
    {
        static $_instance;

        if($_instance === null) {
            $_instance = HttpClient::create();
        }

        return $_instance;
    }

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

    public function isOutdated(GeoLite $geoLite): bool
    {
        $filePath = $geoLite->getDatabasePath();
        $currentSha1 = sha1_file($filePath);
        $newSha1 = $this->getMetadata()['sha'];

        return $currentSha1 !== $newSha1;
    }

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
