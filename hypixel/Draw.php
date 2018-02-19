<?php

class Draw {
    public static $StatRequests = array(
        /*
         * 'id' => array('displayname','return type','object type','func',array(params))
         */
        '0' => array('Name', 'str b','PlayerResult','getFormattedName',array(true),'Name'),
        '1' => array('Final K/D','num','MegaWallsResult','getFinalKDR',array('Overall'),'MW Final K/D'),
        '2' => array('Wins','num','MegaWallsResult','getWins',array('Overall'),'MW Wins'),
        '3' => array('Losses', 'num','MegaWallsResult','getLosses',array('Overall'),'MW Losses'),
        '4' => array('K/D','num','MegaWallsResult','getKDR',array('Overall'),'MW K/D'),
        '5' => array('Final Kills','num','MegaWallsResult','getFinalKills',array('Overall'),'MW Final Kills'),
        '6' => array('Final Deaths','num','MegaWallsResult','getFinalDeaths',array('Overall'),'MW Final Deaths'),
        '7' => array('Class','str','MegaWallsResult','getSelectedClass',array(),'MW Class'),
        '8' => array('W/L','num','MegaWallsResult','getWLR',array('Overall'),'MW W/L'),
        '9' => array('Wither Dmg','num','MegaWallsResult','getWitherDamage',array('Overall'),'MW Wither Dmg'),
        '10' => array('Defending Kills','num','MegaWallsResult','getDefendingKills',array('Overall'),'MW Def Kills'),
        '11' => array('Last Login','num','PlayerResult','getLastLogin',array(),'Last Login'),
        '12' => array('Post-Update Final K/D','num','MegaWallsResult','getPostUpdateFinalKDR',array(),'Post-Update F K/D')
    );
    
    public static function managePage() {
        $urlArray = explode('/',trim($_SERVER['REQUEST_URI'],'/'));
        if(!isset($urlArray[1])) {
            include __DIR__ . '/pages/index.html';
            return;
        }
        switch($urlArray[1]) {
            case 'incompatiblemods':
                $incompatibleMods = array('sidebarmod','oldanimations');
                echo implode(',',$incompatibleMods);
                break;
            case 'reportPlayer':
                $uuid = $urlArray[2];
                $currentReports = json_decode(file_get_contents(__DIR__ . '/apicache/reports.json'),true);
                if(isset($currentReports[$uuid])) {
                    $currentReports[$uuid] = $currentReports[$uuid] + 1;
                } else {
                    $currentReports[$uuid] = 1;
                }
                file_put_contents(__DIR__ . '/apicache/reports.json',json_encode($currentReports));
                echo 'ok';
                break;
            case 'playersToReveal':
                $myArray = explode("\n",preg_replace("/\/\/.*?(\\n|$)/","",trim(file_get_contents(__DIR__ . '/apicache/hackers.txt'))));
                foreach(json_decode(file_get_contents(__DIR__ . '/apicache/reports.json'),true) as $uuid => $reports) {
                    if(!in_array($uuid,$myArray) && $reports>5) {
                        array_push($myArray,$uuid);
                    }
                }
                echo implode(' ',$myArray);
                break;
            case 'addkey':
                echo ApiManager::addApiKey($urlArray[2])?'it worked!':'it didn\'t work :(';
                break;
            case 'gameinput':
                echo ApiManager::saveGameData($urlArray[2]);
                break;
            case 'game':
                try {
                    $gameData = ApiManager::getGameData($urlArray[2]);
                    $gamePage = new GamePage($gameData, $gameData->getSession(), $gameData->getPlayer());
                    $gamePage->drawPage(explode('.',$urlArray[3]));
                } catch (Exception $e) {
                    echo "Could not find that game's data in the system";
                }
                break;
            case 'session': 
                $sessionresult = ApiManager::getSessionResult($urlArray[2], 0);
                if($sessionresult===null || $sessionresult->isSessionNull()) {
                    echo "That player either doesn't exist or isn't in a game. Please try again later.";
                } else {
                    $playerResult = ApiManager::getPlayerResult($urlArray[2], 3600);
                    $sessionPage = new SessionPage($sessionresult,$playerResult);
                    $sessionPage->drawPage(explode('.',$urlArray[3]));
                }
                break;
            case 'player':
                if(strpos($urlArray[2], '?')!==false) {
                    $urlArray[2] = substr($urlArray[2], 0, strpos($urlArray[2], "?"));
                }
                $uuids = array();
                $alreadyLoaded;
                if(file_get_contents(__DIR__ . '/apicache/playerswithname/' . strtolower($urlArray[2]) . '.json')!=false) {
                    $alreadyLoaded = true;
                    $uuids = ApiManager::getAllPlayersWithName($urlArray[2]);
                } else {
                    $alreadyLoaded = false;
                    $nametouuid = ApiManager::getMojangResult($urlArray[2], 3600);
                    if($nametouuid!=null && $nametouuid->isSuccesful()) {
                        $uuids = array($nametouuid->getUUID());
                    } else {
                        $uuids = array();
                    }
                }
                if(sizeof($uuids)==0) {
                    if($alreadyLoaded==false) {
                        echo "Currently, nobody has this name. Checking for players who used to have this name...";
                        ApiManager::getAllPlayersWithName($urlArray[2]);
                        header('Location: ' . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    } else {
                        echo "That player does not exist, are you sure you spelled their name correctly?";
                    }
                    return;
                } else {
                    $playerResults = array();
                    $cacheTime = 3600;
                    if(isset($_GET['refresh'])) {
                        $cacheTime = 0;
                    }
                    foreach($uuids as $uuid) {
                        $playerResult = ApiManager::getPlayerResult($uuid, $cacheTime);
                        if($playerResult->isSuccessful()) {
                            array_push($playerResults,$playerResult);
                        } else {
                            $playerResult = ApiManager::getPlayerResult($uuid, 0);
                            if($playerResult->isSuccessful()) {
                                array_push($playerResults,$playerResult);
                            }
                        }
                    }
                    $allUnsuccessful = true;
                    foreach($playerResults as $playerResult) {
                        if($playerResult->isSuccessful()) {
                            $allUnsuccessful = false;
                        }
                    }
                    if($allUnsuccessful==true) {
                        if($alreadyLoaded==false) {
                            echo "Currently, nobody has this name. Checking for players who used to have this name...";
                            ApiManager::getAllPlayersWithName($urlArray[2]);
                            header('Location: ' . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        } else {
                            echo "That player does not exist, are you sure you spelled their name correctly?";
                        }
                        return;
                    }
                    if(isset($_GET['refresh'])) {
                        header("Location: http://thelagg.com/hypixel/player/$urlArray[2]");
                        exit;
                    }
                    $playerPage = new PlayerPage($playerResults,$alreadyLoaded);
                    $playerPage->drawPage();
                }
                break;
            case 'guild':
                $guildResult;
                if($urlArray[2]==='player') {
                    $guildResult = ApiManager::getGuildResultByPlayer($urlArray[3], 3600);
                } else {
                    $guildResult = ApiManager::getGuildResultByName($urlArray[3], 3600);
                }
                if ($guildResult === null || $guildResult->isSuccessful() !== true) {
                    echo "That guild does not exist, are you sure that player has a guild or that guild exists?";
                    return;
                }
                $guildPage = new GuildPage($guildResult);
                $guildPage->drawPage(explode('.',$urlArray[4]));
                break;
            case 'raw':                
                switch($urlArray[2]) {                
                    case 'player':
                        $r = ApiManager::getPlayerResult($urlArray[3],3600);
                        self::printRawResult($r);
                        break;
                    case 'nameToUUID':
                        $r = ApiManager::getMojangResult($urlArray[3],3600);
                        self::printRawResult($r);
                        break;
                    case 'session':
                        $r = ApiManager::getSessionResult($urlArray[3],0);
                        self::printRawResult($r);
                    break;
                    case 'nameHistory':
                        $r = ApiManager::getMojangResult($urlArray[3],0);
                        self::printRawResult($r); 
                        break;
                    case 'guild':
                        if($urlArray[3]==='player') {
                            $r = ApiManager::getGuildResultByPlayer($urlArray[4], 3600);
                            self::printRawResult($r);
                        } else {
                            $r = ApiManager::getGuildResultByName($urlArray[4], 3600);
                            self::printRawResult($r);
                        }
                        break;
                    default:
                        break;
                }
                break;
            default:
                echo "Sorry, we weren't sure what you were looking for, try another url!";
                break;
        }
    }
    
    /**
     * @param Result $result
     */
    public static function printRawResult($r) {
        $json = $r->getJson();
        if($json===null) {
            return;
        }
        $encoded = json_encode($json);
        echo $encoded;
    }

}
