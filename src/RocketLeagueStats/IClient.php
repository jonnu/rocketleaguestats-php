<?php

namespace Jonnu\RocketLeagueStats;

/**
 * IClient
 */
interface IClient
{

    public function getPlatformsData(Callable $callback = null);
    public function getSeasonsData(Callable $callback = null);
    public function getPlaylistsData(Callable $callback = null);
    public function getTiersData(Callable $callback = null);
    public function getPlayer($uniqueId, $platformId, Callable $callback = null);
    public function searchPlayers($displayName, Callable $callback = null, $page = null);
    public function getRankedLeaderboard($playlistId, Callable $callback);
    public function getStatLeaderboard($statType, Callable $callback);

}
