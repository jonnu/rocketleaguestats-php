<?php

namespace Jonnu\RocketLeagueStats;

use GuzzleHttp;
use Jonnu\RocketLeagueStats\IClient;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Client
 */
class Client implements IClient
{
    const VERSION = '1.0.0';
    const API_URL = 'https://api.rocketleaguestats.com/v1/';

    private $apiToken;
    private $HttpClient;

    /**
     * Client constructor
     *
     * @param string  $apiToken API key
     * @param integer $timeout  Timeout for API calls (default: 2 seconds)
     */
    public function __construct($apiToken, $timeout = 2)
    {
        $this->apiToken   = $apiToken;
        $this->HttpClient = new GuzzleHttp\Client([
            'base_uri'        => static::API_URL,
            'timeout'         => $timeout,
            'allow_redirects' => false
        ]);
    }

    /**
     * Get platforms data
     *
     * This method retrieves platform data.
     *
     * @param callable|null $callback Callback
     *
     * @return mixed
     */
    public function getPlatformsData(Callable $callback = null)
    {
        return $this->call('data/platforms', $callback);
    }

    /**
     * Get seasons data
     *
     * This method retrieves season data.
     *
     * The `endedOn` property in the response is null when a season hasn't ended yet.
     *
     * @param callable|null $callback Callback
     *
     * @return mixed
     */
    public function getSeasonsData(Callable $callback = null)
    {
        return $this->call('data/seasons', $callback);
    }

    /**
     * Get playlists data
     *
     * This method retrieves playlists data.
     *
     * @param callable|null $callback Callback
     *
     * @return mixed
     */
    public function getPlaylistsData(Callable $callback = null)
    {
        return $this->call('data/playlists', $callback);
    }

    /**
     * Get tiers data
     *
     * Returns all season 2 tiers.
     *
     * @param callable|null $callback Callback
     *
     * @return mixed
     */
    public function getTiersData(Callable $callback = null)
    {
        return $this->call('data/tiers', $callback);
    }

    /**
     * Get player data
     *
     * This method retrieves a single player from the API. If it does not exist at RocketLeagueStats,
     * they will check if the player exists on Rocket League's side. If the Rocket League player does
     * not exist, the API will respond with a 404.
     *
     * A player is only said to exist within the API if they have scored _one or more goals_.
     *
     * @param string        $uniqueId   Unique ID [One of the following: Steam 64 ID, PSN Username, Xbox GamerTag or XUID]
     * @param integer       $platformId Platform (Check the Platform Enum)
     * @param callable|null $callback   Callback
     *
     * @return mixed
     */
    public function getPlayer($uniqueId, $platformId, Callable $callback = null)
    {
        return $this->call('player', $callback, [
            'unique_id'   => $uniqueId,
            'platform_id' => $platformId,
        ]);
    }

    /**
     * Search players
     *
     * This method retrieves multiple players.
     * It will only search for players that exist in the API's database, NOT all rocket league players.
     *
     * @param string        $displayName User's display name
     * @param callable|null $callback    Callback
     * @param integer|null  $page        Page (optional)
     *
     * @return mixed
     */
    public function searchPlayers($displayName, Callable $callback = null, $page = null)
    {
        return $this->call('search/players', $callback, [
            'display_name' => $displayName,
            'page'         => $page ?? 0,
        ]);
    }

    /**
     * Get ranked leaderboard
     *
     * Retrieves an array of 100 players sorted by their season 2 rank points.
     *
     * @param integer       $playlistId Playlist (Check the RankedPlaylist Enum)
     * @param callable|null $callback   Callback
     *
     * @return mixed
     */
    public function getRankedLeaderboard($playlistId, Callable $callback)
    {
        return $this->call('leaderboard/ranked', $callback, [
            'playlist_id' => $playlistId,
        ]);
    }

    /**
     * Get stat leaderboard
     *
     * Retrieves an array of 100 players sorted by their specified stat amount.
     *
     * @param integer       $statType Statistic type (Check the StatType Enum)
     * @param callable|null $callback Callback
     *
     * @return mixed
     */
    public function getStatLeaderboard($statType, Callable $callback)
    {
        return $this->call('leaderboard/stat', $callback, [
            'type' => $statType,
        ]);
    }

    /**
     * Returns client name/version
     *
     * @return string
     */
    public function getVersion()
    {
        return sprintf('rocketleaguestats-php (v%s)', static::VERSION);
    }

    private function call($endpoint, Callable $callback = null, $query = [])
    {
        if (!empty($query)) {
            $options['query'] = $query;
        }

        $options['headers'] = [
            'Accept' => 'application/json',
            'User-Agent' => $this->getVersion(),
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

    private function handleException(RequestException $Exception)
    {
        echo $Exception->getMessage();
        exit(1);
    }

}
