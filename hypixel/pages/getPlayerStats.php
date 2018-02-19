<?php

require_once __DIR__ . '/../IncludeAll.php';
$uuid = $_GET["uuid"];
$stats = SessionPage::fixExtraValues(explode('.',$_GET['stats']));

$player = ApiManager::getPlayerResult($uuid, 3600);
$mw = $player->getMegaWallsResult();
$row = [];

foreach($stats as $id) {
    switch(Draw::$StatRequests[$id][2]) {
        case 'PlayerResult':
            $val = call_user_func_array(array($player,Draw::$StatRequests[$id][3]), Draw::$StatRequests[$id][4]);
            $row[$id] = $val;
            break;
        case 'MegaWallsResult':
            $val = call_user_func_array(array($mw,Draw::$StatRequests[$id][3]), Draw::$StatRequests[$id][4]);
            $row[$id] = $val;
            break;
        default:
            break;
    }
}

echo $uuid . '--';

foreach($row as $key => $value) {
    $justify = 'left';
    $bold = '';
    if(strpos(Draw::$StatRequests[$key][1], ' b') !== false) {
        $bold = ' font-weight: bold;';
    }
    if(strpos(Draw::$StatRequests[$key][1], 'num') !== false) {
        $justify = 'right';
    }
    if($value===null || trim($value)==='nan' || is_nan($value)) {
      echo "<td style=\"text-align:$justify;$bold\">N/A</td>";
    } else {
      echo "<td style=\"text-align:$justify;$bold\">$value</td>";
    }
}
