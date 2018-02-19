<?php
require_once __DIR__ . '/Utils.php';

/**
 * Manages hypixel and mojang api calls, uses static methods to return
 * whichever Result you're looking for
 *
 * @author ddude
 */
class ApiManager {
    /**
     * loops through api keys for one you can use, 
     * makes sure to update the api key file with new
     * timestamp for last use of specific api key taken
     * @return type
     */
    private static function getApiKey() {
        $content = file_get_contents(__DIR__ . "/apicache/apikeys.txt");
        $keypairs = explode("\n",$content);
        foreach($keypairs as $keypair) {
            if(trim($keypair)==='') {continue;}
            $pair = explode(' ',$keypair);
            $key = $pair[0];
            $lastUse = $pair[1];
            $inUse = trim($pair[2]);
            if($inUse=="false") {
                file_put_contents(__DIR__ . "/apicache/apikeys.txt",preg_replace("/$key $lastUse false/", "$key $lastUse true", file_get_contents(__DIR__ . '/apicache/apikeys.txt')));
                if(Utils::get_millisecond()-$lastUse<500) {
                    usleep(1000*(500-(Utils::get_millisecond()-$lastUse)));
                }
                $contentsnew = preg_replace("/$key $lastUse true\\n/","",file_get_contents(__DIR__ . "/apicache/apikeys.txt")) . "$key " . Utils::get_millisecond() . " false\n";
                file_put_contents(__DIR__ . '/apicache/apikeys.txt',$contentsnew);
                return $key;
            }
        }
        return null;
    }
    
    private static function removeApiKey($key) {
        $content = file_get_contents(__DIR__ . "/apicache/apikeys.txt");
        $newcontent = preg_replace("/$key \d+ \S+\\n/","",$content);
        file_put_contents(__DIR__ . '/apicache/apikeys.txt',$newcontent);
        file_put_contents(__DIR__ . '/apicache/badapikeys.txt',file_get_contents(__DIR__ . '/apicache/badapikeys.txt') . $key . "\n");
    }
    
    private static function checkIfApiKeyOk($result,$key) {
        if(trim($result)=='{"success":false,"cause":"Invalid API key!"}') {
            self::removeApiKey($key);
            return false;
        }
        return true;
    }
    
    /**
     * @param string $name
     * @return array(uuid)
     */
    public static function getAllPlayersWithName($name) {
        $decrement = 2592000*2-1;
        $time = time();
        $end = 1423008000;
        $playerArray = array();
        $firstPerson = true;
        $existingContent = file_get_contents(__DIR__ . '/apicache/playerswithname/' . strtolower($name) . '.json');
        if($existingContent!==false) {
            $existingJson = json_decode($existingContent,true);
            $playerArray = $existingJson;
            $end = $existingJson['timeRequested'];
            unset($playerArray['timeRequested']);
        }
        while($time>=$end) {
            while(self::canRunMojang()===false) {
                usleep(20);
            }
            $content = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$name?at=$time");
            if($content!==false) {
                $nameToUUID = new NameToUUIDResult(json_decode($content,true));
                if($nameToUUID->isSuccesful()) {
                    $inArray = false;
                    foreach($playerArray as $uuid) {
                        if($uuid==$nameToUUID->getUUID()) {
                            $inArray = true;
                        }
                    }
                    $timeOfName = -1;
                    if($firstPerson==true) {
                        $nameHistory = self::getMojangResult($nameToUUID->getUUID(), 3600);
                        $timeOfName = $nameHistory->getTimeOfName($name);
                        $firstPerson = false;
                    }
                    if($timeOfName!=-1 && $inArray==false) {
                        $time = min(array($timeOfName-$decrement,$time - $decrement));
                        array_push($playerArray,$nameToUUID->getUUID());
                        continue;
                    } else if ($inArray==false) {
                        array_push($playerArray,$nameToUUID->getUUID());
                    }
                }
            }
            $time = $time - $decrement;
        }
        $copiedArray = array_flip(array_flip($playerArray));
        $copiedArray['timeRequested'] = time();
        file_put_contents(__DIR__ . '/apicache/playerswithname/' . strtolower($name) . '.json', json_encode($copiedArray));
        return $playerArray;
    }
    
    private static function checkKeyValid($key) {
        $test = file_get_contents("https://api.hypixel.net/player?key=$key&uuid=6f609581-bbfc-4d43-8771-25f95a9922f9");
        if(trim($test)=='{"success":false,"cause":"Invalid API key!"}') {
            return false;
        } 
        return true;
    }
    
    public static function addApiKey($key) {
        $keysFile = file_get_contents(__DIR__ . '/apicache/apikeys.txt');
        if(($keysFile!==false && strpos($keysFile, $key) !== false) || !self::checkKeyValid($key)) {
            return false;
        } else {
            $text = $key . ' 0 false\n';
            file_put_contents(file_get_contents(__DIR__ . '/apicache/apikeys.txt') . $text);
            return true;
        }
    }
    
    /**
     * Decides whether it's an ok time to run a mojang uuid/username
     * search, based on last recorded use of the mojang api
     * @return boolean
     */
    private static function canRunMojang() {
        $content = file_get_contents(__DIR__ . "/apicache/mojangLastUse.txt");
        if($content===false) {
            return false;
        }
        if(Utils::get_millisecond()-$content<1000) {
            return false;
        } else {
            file_put_contents(__DIR__ . "/apicache/mojangLastUse.txt",Utils::get_millisecond());
            return true;
        }
    }
    
    public static function saveGameData($jsonText) {
        $jsonText = urldecode($jsonText);
        $json = json_decode($jsonText,true);
        if($json!=false) {
            $id = uniqid();
            while(file_get_contents(__DIR__ . "/apicache/gamedata/$id.json")!=false) {
                $id = uniqid();
            }
            $works = true;
            try {
                $gameresult = new GameResult($json);
            } catch (Exception $ex) {
                $works = false;
            }
            if($works===true) {
                file_put_contents(__DIR__ . "/apicache/gamedata/$id.json", json_encode($json));
                return $id;
            }
        }
        return "error";
    }
    
    public static function getGameData($id) {
        $file = file_get_contents(__DIR__ . "/apicache/gamedata/$id.json");
        if(file!=false) {
            return new GameResult(json_decode($file,true));
        }
        throw new Exception("Could not find file for that game's data");
    }
    
    /**
     * returns result for player, taking name or uuid as input, output is an array (decoded from json)
     * 
     * @param string $input
     * @param type $maxTime
     * @return type
     */
    public static function getPlayerResult($input, $maxTime) { //maxTime in seconds (not milliseconds)
        if(strlen($input)!==36 && strlen($input)!==32) {
            $mojangResult = self::getMojangResult($input);
            if($mojangResult===null) return null;
            $uuid = $mojangResult->getUUID();
            if($uuid===null) return null;
            return self::getPlayerResult($uuid,$maxTime);
        } else {
            if(strlen($input)===32) {
                $input = substr($input,0,8) . '-' . substr($input,8,4) . '-' . substr($input,12,4) . '-' . substr($input,16,4) . '-' . substr($input,20,12);
            }
            $file = file_get_contents(__DIR__ . "/apicache/hypixel/player/$input.json");
            if($file!=false) {
                $json = json_decode($file,true);
                $timeRequested = $json['timeRequested']; //seconds
                if(time()-$timeRequested<$maxTime) {
                    return new PlayerResult($json);
                }
            }
            $apiKey = self::getApiKey();
            while($apiKey===null) {
                $apiKey = self::getApiKey();
            }
            $raw = file_get_contents("https://api.hypixel.net/player?key=$apiKey&uuid=$input");
            if($raw===false) return null;
            if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
                return self::getPlayerResult($input, $maxTime);
            }
            $json = json_decode($raw,true);
            if($json['success']===false) return null;
            $json['timeRequested'] = time();
            $encoded = json_encode($json);
            file_put_contents(__DIR__ . "/apicache/hypixel/player/$input.json",$encoded);
            return new PlayerResult($json);
        }
    } 

    private static function addUuidDashes($uuid) {
        return substr($uuid,0,8) . '-' . substr($uuid,8,4) . '-' . substr($uuid,12,4) . '-' . substr($uuid,16,4) . '-' . substr($uuid,20,12);
    }
    
    private function getGuildFromId($id,$maxTime) {
        $file = file_get_contents(__DIR__ . "/apicache/hypixel/guild/$id.json");
        if($file!=false) {
            $json = json_decode($file,true);
            $timeRequested = $json['timeRequested']; //seconds
            if(time()-$timeRequested<$maxTime) {
                return new GuildResult($json);
            }
        }
        $apiKey = self::getApiKey();
        while($apiKey===null) {
            $apiKey = self::getApiKey();
        }
        $raw = file_get_contents("https://api.hypixel.net/guild?key=$apiKey&id=$id");
        if($raw===false) return null;
        if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
            return self::getGuildFromId($id, $maxTime);
        }        
        $json = json_decode($raw,true);
        if($json['success']===false) return null;
        $json['timeRequested'] = time();
        $encoded = json_encode($json);
        file_put_contents(__DIR__ . "/apicache/hypixel/guild/$id.json",$encoded);
        return new GuildResult($json);        
    }
    
    public static function getGuildResultByPlayer($input, $maxTime) {
        if(strlen($input)!==36 && strlen($input)!==32) {
            $mojangResult = self::getMojangResult($input);
            if($mojangResult===null) return null;
            $uuid = $mojangResult->getUUID();
            if($uuid===null) return null;
            return self::getGuildResultByPlayer($uuid,$maxTime);
        } else {
            if(strlen($input)===32) {
                $input = self::addUuidDashes($input);
            }
            $file = file_get_contents(__DIR__ . "/apicache/hypixel/findGuild/$input.json");
            if($file!=false) {
                $json = json_decode($file,true);
                $timeRequested = $json['timeRequested']; //seconds
                if(time()-$timeRequested<$maxTime) {
                    if($json['guild']===null) return null;
                    return self::getGuildFromId($json['guild'],$maxTime);
                }
            }
            $apiKey = self::getApiKey();
            while($apiKey===null) {
                $apiKey = self::getApiKey();
            }
            $raw = file_get_contents("https://api.hypixel.net/findGuild?key=$apiKey&byUuid=$input");
            if($raw===false) return null;
            if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
                return self::getGuildResultByPlayer($input, $maxTime);
            }    
            $json = json_decode($raw,true);
            if($json['success']===false) return null;
            $json['timeRequested'] = time();
            $encoded = json_encode($json);
            file_put_contents(__DIR__ . "/apicache/hypixel/findGuild/$input.json",$encoded);
            if($json['guild']===null) return null;
            return self::getGuildFromId($json['guild'],$maxTime);
        }
    }
    
    public static function getGuildResultByName($name,$maxTime) {
        $file = file_get_contents(__DIR__ . "/apicache/hypixel/findGuild/$name.json");
        if($file!=false) {
            $json = json_decode($file,true);
            $timeRequested = $json['timeRequested']; //seconds
            if(time()-$timeRequested<$maxTime) {
                if($json['guild']===null) return null;
                return self::getGuildFromId($json['guild'],$maxTime);
            }
        }
        $apiKey = self::getApiKey();
        while($apiKey===null) {
            $apiKey = self::getApiKey();
        }
        $raw = file_get_contents("https://api.hypixel.net/findGuild?key=$apiKey&byName=$name");
        if($raw===false) return null;
        if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
            return self::getGuildResultByName($name, $maxTime);
        }
        $json = json_decode($raw,true);
        if($json['success']===false) return null;
        $json['timeRequested'] = time();
        $encoded = json_encode($json);
        file_put_contents(__DIR__ . "/apicache/hypixel/findGuild/$name.json",$encoded);
        if($json['guild']===null) return null;
        return self::getGuildFromId($json['guild'],$maxTime);
    }
    
    public static function getLeaderboardsResult($maxTime) {
        $file = file_get_contents(__DIR__ . "/apicache/hypixel/leaderboards.json");
        if($file!=false) {
            $json = json_decode($file,true);
            $timeRequested = $json['timeRequested']; //seconds
            if(time()-$timeRequested<$maxTime) {
                return $json;
            }
        }
        $apiKey = self::getApiKey();
        while($apiKey===null) {
            $apiKey = self::getApiKey();
        }
        $raw = file_get_contents("https://api.hypixel.net/leaderboards?key=$apiKey");
        if($raw===false) return null;
        if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
            return self::getLeaderboardsResult($maxTime);
        }
        $json = json_decode($raw,true);
        if($json['success']===false) return null;
        $json['timeRequested'] = time();
        $encoded = json_encode($json);
        file_put_contents(__DIR__ . "/apicache/hypixel/leaderboards.json",$encoded);
        return $json;
    }
    
    public static function getFriendsResult($input,$maxTime) {
        if(strlen($input)!==36 && strlen($input)!==32) {
            $mojangResult = self::getMojangResult($input);
            if($mojangResult===null) return null;
            $uuid = $mojangResult->getUUID();
            if($uuid===null) return null;
            return self::getFriendsResult($uuid,$maxTime);
        } else {
            if(strlen($input)===32) {
                $input = self::addUuidDashes($input);
            }
            $file = file_get_contents(__DIR__ . "/apicache/hypixel/friends/$input.json");
            if($file!=false) {
                $json = json_decode($file,true);
                $timeRequested = $json['timeRequested']; //seconds
                if(time()-$timeRequested<$maxTime) {
                    return $json;
                }
            }
            $apiKey = self::getApiKey();
            while($apiKey===null) {
                $apiKey = self::getApiKey();
            }
            $raw = file_get_contents("https://api.hypixel.net/friends?key=$apiKey&uuid=$input");
            if($raw===false) return null;
            if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
                return self::getFriendsResult($input,$maxTime);
            }
            $json = json_decode($raw,true);
            if($json['success']===false) return null;
            $json['timeRequested'] = time();
            $encoded = json_encode($json);
            file_put_contents(__DIR__ . "/apicache/hypixel/friends/$input.json",$encoded);
            return $json;
        }
    }

    public static function getSessionResult($input,$maxTime) {
        if(strlen($input)!==36 && strlen($input)!==32) {
            $mojangResult = self::getMojangResult($input);
            if($mojangResult===null) return null;
            $uuid = $mojangResult->getUUID();
            if($uuid===null) return null;
            return self::getSessionResult($uuid,$maxTime);
        } else {
            if(strlen($input)===32) {
                $input = self::addUuidDashes($input);
            }
            $file = file_get_contents(__DIR__ . "/apicache/hypixel/session/$input.json");
            if($file!=false) {
                $json = json_decode($file,true);
                $timeRequested = $json['timeRequested']; //seconds
                if(time()-$timeRequested<$maxTime) {
                    return new SessionResult($json);
                }
            }
            $apiKey = self::getApiKey();
            while($apiKey===null) {
                $apiKey = self::getApiKey();
            }
            $raw = file_get_contents("https://api.hypixel.net/session?key=$apiKey&uuid=$input");
            if($raw===false) return null;
            if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
                return self::getSessionResult($input,$maxTime);
            }
            $json = json_decode($raw,true);
            if($json['success']===false) return null;
            $json['timeRequested'] = time();
            $encoded = json_encode($json);
            file_put_contents(__DIR__ . "/apicache/hypixel/session/$input.json",$encoded);
            return new SessionResult($json);
        }
    }
    
    public static function getKeyResult($key,$maxTime) {
        $file = file_get_contents(__DIR__ . "/apicache/hypixel/keys/$key.json");
        if($file!=false) {
            $json = json_decode($file,true);
            $timeRequested = $json['timeRequested']; //seconds
            if(time()-$timeRequested<$maxTime) {
                return $json;
            }
        }
        $raw = file_get_contents("https://api.hypixel.net/key?key=$key");
        if($raw===false) return null;
        if(self::checkIfApiKeyOk($raw, $key)===false) {
            return self::getKeyResult($key,$maxTime);
        }
        $json = json_decode($raw,true);
        if($json['success']===false) return null;
        $json['timeRequested'] = time();
        $encoded = json_encode($json);
        file_put_contents(__DIR__ . "/apicache/hypixel/keys/$key.json",$encoded);
        return $json;
    }

    public static function getWatchdogStatsResult($maxTime) {
        $file = file_get_contents(__DIR__ . "/apicache/hypixel/watchdogstats.json");
        if($file!=false) {
            $json = json_decode($file,true);
            $timeRequested = $json['timeRequested']; //seconds
            if(time()-$timeRequested<$maxTime) {
                return $json;
            }
        }
        $apiKey = self::getApiKey();
        while($apiKey===null) {
            $apiKey = self::getApiKey();
        }
        $raw = file_get_contents("https://api.hypixel.net/watchdogstats?key=$apiKey");
        if($raw===false) return null;
        if(self::checkIfApiKeyOk($raw, $apiKey)===false) {
            return self::getWatchdogStatsResult($maxTime);
        }
        $json = json_decode($raw,true);
        if($json['success']===false) return null;
        $json['timeRequested'] = time();
        $encoded = json_encode($json);
        file_put_contents(__DIR__ . "/apicache/hypixel/watchdogstats.json",$encoded);
        return $json;
    }
    
    /**
     * takes either player name or uuid as input, finds mojang api result
     * for the specific input (name history for uuid, uuid lookup for username)
     * as an array
     * 
     * @param type $input
     * @param type $maxTime
     * @return type
     */
    public static function getMojangResult($input,$maxTime) {
        if(strlen($input)!==36 && strlen($input)!==32) {
            $json = file_get_contents(__DIR__ . "/apicache/mojang/name/$input.json");
            if($json!=false) {
                $decoded = json_decode($json,true);
                if(time()-$decoded['timeRequested']<$maxTime) {
                    return new NameToUUIDResult($decoded);
                }
            }
            while(self::canRunMojang()===false) {
                usleep(20);
            }
            $raw = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$input?at=" . time());
            if($raw===false) return null;
            $json2 = json_decode($raw,true);
            if($json2["id"]===null) return null;
            $json2['timeRequested'] = time();
            $encoded = json_encode($json2);
            file_put_contents(__DIR__ . "/apicache/mojang/name/$input.json",$encoded);
            return new NameToUUIDResult($json2);
        } else {
            if(strlen($input)===36) {
                $input = preg_replace('/-/','',$input);
            }
            $json = file_get_contents(__DIR__ . "/apicache/mojang/uuid/$input.json");
            if($json!=false) {
                $decoded = json_decode($json,true);
                if(time()-$decoded['timeRequested']<$maxTime) {
                    return new NameHistoryResult($decoded);
                }
            }
            while(self::canRunMojang()===false) {
                usleep(20);
            }
            $raw = file_get_contents("https://api.mojang.com/user/profiles/$input/names");
            if($raw===false) return null;
            $json = json_decode($raw,true);
            if($json[0]===null || $json[0]['name']===null) return null;
            $json['timeRequested'] = time();
            $encoded = json_encode($json);
            file_put_contents(__DIR__ . "/apicache/mojang/uuid/$input.json",$encoded);
            return new NameHistoryResult($json);
        }
    }
}