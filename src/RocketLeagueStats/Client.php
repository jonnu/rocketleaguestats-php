<?php

namespace Jonnu\RocketLeagueStats;

use Jonnu\RocketLeagueStats\IClient;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Client
 */
class Client implements IClient
{

    private $apiToken;
    private $apiUrl = 'https://api.rocketleaguestats.com/v1/';
    private $HttpClient;

    public function __construct($apiToken, $timeout = 2)
    {
        $this->apiToken   = $apiToken;
        $this->HttpClient = new HttpClient([
            'base_uri'        => $this->apiUrl,
            'timeout'         => $timeout,
            'allow_redirects' => false
        ]);
    }

    public function getPlatformsData(Callable $callback = null)
    {
        return $this->call('/data/platforms', $callback);
    }

    public function getSeasonsData(Callable $callback = null)
    {
        return $this->call('/data/seasons', $callback);
    }

    public function getPlaylistsData(Callable $callback = null)
    {
        return $this->call('/data/seasons', $callback);
    }

    public function getTiersData(Callable $callback = null)
    {
        return $this->call('/data/tiers', $callback);
    }

    public function getPlayer($uniqueId, $platformId, Callable $callback = null)
    {
        return $this->call('player', $callback, [
            'unique_id'   => $uniqueId,
            'platform_id' => $platformId,
        ]);
    }

    public function searchPlayers($displayName, Callable $callback = null, $page = null)
    {
        return $this->call('search/players', $callback, [
            'display_name' => $displayName,
            'page'         => $page ?? 0,
        ]);
    }

    public function getRankedLeaderboard($playlistId, Callable $callback)
    {
        return $this->call('leaderboard/ranked', $callback, [
            'playlist_id' => $playlistId,
        ]);
    }

    public function getStatLeaderboard($statType, Callable $callback)
    {
        return $this->call('leaderboard/ranked', $callback, [
            'type' => $statType,
        ]);
    }

    private function handleException(RequestException $Exception)
    {
        echo "EXCEPTION:\n" . $Exception->getMessage();
        die();
    }

    private function call($endpoint, Callable $callback = null, $query = [])
    {
        if (!empty($query)) {
            $options['query'] = $query;
        }

        $options['headers'] = [
            'Accept' => 'application/json',
            'User-Agent' => $this->getUserAgentString(),
            'Authorization' => sprintf('Bearer %s', $this->apiToken),
        ];

        $doneCallable = function (ResponseInterface $Response) use ($callback) {
            return $callback($Response->getStatusCode(), $Response->getBody()->getContents());
        };

        $errorCallable = function (RequestException $Exception) {
            return $this->handleException($Exception);
        };

        $Promise = $this->HttpClient->requestAsync('GET', $endpoint, $options);
        return $Promise->then($doneCallable, $errorCallable)->wait();
    }

    private function getUserAgentString()
    {
        return 'php-rls (v1.0)';
    }

}
