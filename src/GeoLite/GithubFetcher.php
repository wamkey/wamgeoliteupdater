<?php

namespace Wamkey\GeoLiteUpdater\GeoLite;

use Symfony\Component\HttpClient\HttpClient;
use Wamkey\GeoLiteUpdater\GeoLite\Contracts\FetcherInterface;

class GithubFetcher implements FetcherInterface
{
    protected $repositoryName = '';

    protected $repositoryContext = '';

    protected $repositoryPath = '';

    public function __construct()
    {
        $this->repositoryName = 'P3TERX/GeoLite.mmdb';
        $this->repositoryContext = 'download';
        $this->repositoryPath = 'GeoLite2-City.mmdb';
    }

    public function getMetadata(): array
    {
        $http = HttpClient::create();
        $response = $http->request(
            'GET',
            'https://api.github.com/repos/' . $this->repositoryName
                . '/contents/' . $this->repositoryPath . 'GeoLite2-City.mmdb?ref=' . $this->repositoryContext,
            [
                'headers' => [
                    'Connection' => 'keep-alive',
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Accept' => 'application/vnd.github+json',
                ]
            ]
        );
        dd($response);
    }

    public function isOutdated(GeoLite $geoLite): bool
    {
        // TODO: Implement isOutdated() method.
    }

    /**
     * File path.
     *
     * @return string
     */
    public function download(): string
    {

    }
}
