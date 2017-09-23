<?php

require 'vendor/autoload.php';

use Jonnu\RocketLeagueStats\Client;
use Jonnu\RocketLeagueStats\Enum\Platform;

$Client = new Client("your-api-key");

echo "\n" . $Client->getVersion() . "\n\n";

$Client->getPlayer('76561198033338223', Platform::STEAM, function ($Status, $Data) {
    if ($Status === 200) {
        echo "-- Player Data:\n";
        echo "   Display name: " . $Data->displayName . "\n";
        echo "   Goals: " . $Data->stats->goals . "\n\n";
    }
});
