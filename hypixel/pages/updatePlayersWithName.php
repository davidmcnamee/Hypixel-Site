<?php

require_once __DIR__ . '/../IncludeAll.php';
$name = $_GET["name"];
$uuids = ApiManager::getAllPlayersWithName($name);
unset($uuids[0]);
$playerResults = array();
foreach($uuids as $uuid) {
    $playerResult = ApiManager::getPlayerResult($uuid, 3600);
    if($playerResult->isSuccessful()) {
        array_push($playerResults,$playerResult);
    }
}
$playerPage = new PlayerPage($playerResults,true);
foreach($playerResults as $playerResult) {
    $playerPage->drawPlayerModule($playerResult, false);
}