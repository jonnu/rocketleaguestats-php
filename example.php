<?php

require 'vendor/autoload.php';

use Jonnu\RocketLeagueStats\Client;
use Jonnu\RocketLeagueStats\Enum\Platform;

$Client = new Client("test");

$Client->getPlayer('76561198033338223', Platform::STEAM, function ($Status, $Data) {
    if ($Status === 200) {
        echo "-- Player Data:";
        echo "   Display name: " . $Data->displayName;
        echo "   Goals: " . $Data->stats->goals;
    }
});