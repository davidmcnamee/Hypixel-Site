<?php

require_once __DIR__ . '/../Result.php';

require_once __DIR__ . '/games/ArcadeResult.php';
require_once __DIR__ . '/games/ArenaResult.php';
require_once __DIR__ . '/games/BedwarsResult.php';
require_once __DIR__ . '/games/BlitzResult.php';
require_once __DIR__ . '/games/CopsAndCrimsResult.php';
require_once __DIR__ . '/games/CrazyWallsResult.php';
require_once __DIR__ . '/games/LegacyResult.php';
require_once __DIR__ . '/games/MegaWallsResult.php';
require_once __DIR__ . '/games/MurderMysteryResult.php';
require_once __DIR__ . '/games/PaintballResult.php';
require_once __DIR__ . '/games/QuakeCraftResult.php';
require_once __DIR__ . '/games/SkyclashResult.php';
require_once __DIR__ . '/games/SkywarsResult.php';
require_once __DIR__ . '/games/SmashHeroesResult.php';
require_once __DIR__ . '/games/SpeedUhcResult.php';
require_once __DIR__ . '/games/TntGamesResult.php';
require_once __DIR__ . '/games/TurboKartRacersResult.php';
require_once __DIR__ . '/games/UhcResult.php';
require_once __DIR__ . '/games/VampireZResult.php';
require_once __DIR__ . '/games/WallsResult.php';
require_once __DIR__ . '/games/WarlordsResult.php';

/**
 * Description of PlayerResult
 *
 * @author ddude
 */
class PlayerResult extends Result {
    private $arcadeResult;
    private $arenaResult;
    private $warlordsResult;
    private $blitzResult;
    private $copsAndCrimsResult;
    private $paintballResult;
    private $quakeCraftResult;
    private $tntGamesResult;
    private $uhcResult;
    private $vampireZResult;
    private $wallsResult;
    private $megaWallsResult;
    private $turboKartRacersResult;
    private $skywarsResult;
    private $crazyWallsResult;
    private $smashHeroesResult;
    private $speedUhcResult;
    private $skyclashResult;
    private $legacyResult;
    private $bedwarsResult;
    private $murderMysteryResult;
    
    public function __construct($array) {
        $this->json = $array;
        
        $stats = $array['player']['stats'];
        $this->arcadeResult = new ArcadeResult($stats['Arcade']);
        $this->arenaResult = new ArenaResult($stats['Arena']);
        $this->warlordsResult = new WarlordsResult($stats['Battleground']);
        $this->blitzResult = new BlitzResult($stats['HungerGames']);
        $this->copsAndCrimsResult = new CopsAndCrimsResult($stats['MCGO']);
        $this->paintballResult = new PaintballResult($stats['Paintball']);
        $this->quakeCraftResult = new QuakeCraftResult($stats['Quake']);
        $this->tntGamesResult = new TntGamesResult($stats['TNTGames']);
        $this->uhcResult = new UhcResult($stats['UHC']);
        $this->vampireZResult = new VampireZResult($stats['VampireZ']);
        $this->wallsResult = new WallsResult($stats['Walls']);
        $this->megaWallsResult = new MegaWallsResult($stats['Walls3']);
        $this->turboKartRacersResult = new TurboKartRacersResult($stats['GingerBread']);
        $this->skywarsResult = new SkywarsResult($stats['SkyWars']);
        $this->crazyWallsResult = new CrazyWallsResult($stats['TrueCombat']);
        $this->smashHeroesResult = new SmashHeroesResult($stats['SuperSmash']);
        $this->speedUhcResult = new SpeedUhcResult($stats['SpeedUHC']);
        $this->skyclashResult = new SkyclashResult($stats['SkyClash']);
        $this->legacyResult = new LegacyResult($stats['Legacy']);
        $this->bedwarsResult = new BedwarsResult($stats['Bedwars']);
        $this->murderMysteryResult = new MurderMysteryResult($stats['MurderMystery']);

    }
    
    /**
     * 
     * @param NameToUUIDResult $nameToUUIDResult
     */
    public function draw() {
        ?>
            <div class="row">
            <div class="col-md-6">
                <?php
                echo '<br/><img src="https://crafatar.com/renders/body/' . $this->getUUID() . '?overlay">';
                echo '<br/><br/>' . $this->getOnlineStatusStr(); 
                echo '<br/><b>NetworkLevel: </b>' . $this->getNetworkLevel();
                echo '<br/><b>Total Exp: </b>' . $this->getTotalExp();
                echo '<br/><b>Multiplier: </b>' . $this->getMultiplier() . 'x';
                echo '<br/><b>Exp Progress: </b>' . $this->getExpProgress() . '%';   
                echo '<br/><b>Achievement Points: </b>' . $this->getAchievementPoints();
                echo '<br/><b>Last Login: </b>' . $this->getLastLogin();
                echo '<br/><b>UUID: </b>' . $this->getUUID();
                echo '<br/><b>      </b>' . Utils::formatUUID($this->getUUID());
                ?>
            </div>
            <div class="col-md-6">
                <?php
                $namehistory = ApiManager::getMojangResult($this->getUUID(), 3600);
                $namehistory->printNameHistory();
                ?>
            </div>
            </div>    
     <?php    
    }
    
    public function getUUID() {
        return $this->json['player']['uuid'];
    }
    
    public function isSuccessful() {
        return $this->json['success']!==null && $this->json['player']!==null;
    }
    
    public function getUnformattedName() {
        return $this->json['player']['displayname'];
    }
    
    private static function getLevel($exp) {
        $BASE = 10000.0;
        $GROWTH = 2500.0;
        $HALF_GROWTH = 0.5 * $GROWTH;
        $REVERSE_PQ_PREFIX = -($BASE - 0.5 * $GROWTH) / $GROWTH;
        $REVERSE_CONST = $REVERSE_PQ_PREFIX * $REVERSE_PQ_PREFIX;
        $GROWTH_DIVIDES_2 = 2 / $GROWTH;
        return $exp<0 ? 1 : floor(1 + $REVERSE_PQ_PREFIX + sqrt($REVERSE_CONST + $GROWTH_DIVIDES_2 * $exp));
    }

    private static function getExactLevel($exp) {
            return self::getLevel($exp) + self::getPercentageToNextLevel($exp);
    }

    private static function getExpFromLevelToNext($level) {
        $BASE = 10000.0;
        $GROWTH = 2500.0;
        return $level < 1 ? $BASE : $GROWTH * ($level - 1) + $BASE;
    }

    private static function getTotalExpToLevel($level) {
        $lv = floor($level);
        $x0 = self::getTotalExpToFullLevel($lv);
        if ($level === $lv) return $x0;
        return (self::getTotalExpToFullLevel($lv + 1) - $x0) * ($level % 1) + $x0;
    }

    private static function getTotalExpToFullLevel($level) {
        $BASE = 10000.0;
        $GROWTH = 2500.0;
        $HALF_GROWTH = 0.5 * $GROWTH;
        return ($HALF_GROWTH * ($level - 2) + $BASE) * ($level - 1);
    }

    private static function getPercentageToNextLevel($exp) {
        $lv = self::getLevel($exp);
        $x0 = self::getTotalExpToLevel($lv);
        return ($exp - $x0) / (self::getTotalExpToLevel($lv + 1) - $x0);
    }
    
    private static function getLevelMultiplier($level) {
        if ($level >= 250) return 8;
        if ($level >= 200) return 7;
        if ($level >= 150) return 6.5;
        if ($level >= 125) return 6; 
        if ($level >= 100) return 5.5;
        if ($level >= 50) return 5;
        if ($level >= 40) return 4.5;
        if ($level >= 30) return 4;
        if ($level >= 25) return 3.5;
        if ($level >= 20) return 3;
        if ($level >= 15) return 2.5;
        if ($level >= 10) return 2;
        if ($level >= 5) return 1.5;
        return 1;
    }
    
    public function getLastLoginLong() {
        return (isset($this->json['player']['lastLogin'])?$this->json['player']['lastLogin']:(time()*1000)) - 3600*1000*5;
    }
    
    public function getLastLogoutLong() {
        return (isset($this->json['player']['lastLogout'])?$this->json['player']['lastLogout']:(time()*1000)) - 3600*1000*5;
    }
    
    public function getOnlineStatusStr() {
        $str;
        if($this->getLastLoginLong()>$this->getLastLogoutLong()) {
            $str = '<b><span style="color:green">Online</span></b>';
        } else {
            $str = '<b><span style="color:red">Offline</span></b>';
        }
        $str = $str . ' as of ' . $this->getTimeRequestedStr();
        return $str;
    }
    
    public function getLastLogin() {
        $time = $this->getLastLoginLong()/1000;
        return date('g:ia m/d/Y',$time);
    }
    
    public function getTimeRequestedStr() {
        $time = $this->json['timeRequested'] - 60*60*5;
        return date('g:ia m/d/Y',$time);
    }
    
    public function getAchievementPoints() {
        $allachievements = include(__DIR__ . "/../Achievements.php");
        $allachievements = $allachievements['achievements'];
        $count = 0;
        foreach($allachievements as $gamename => $gamearray) {        
            $tiered = $gamearray['tiered'];
            $onetime = $gamearray['one_time'];
            foreach($onetime as $name => $value) {
                $name = strtolower($name);
                if(in_array($gamename . "_" . $name,$this->json['player']['achievementsOneTime'])) {    
                    $count += $value['points'];
                }
            }
            foreach($tiered as $name => $value) {
                $name = strtolower($name);
                $tier = 0;
                $myvalue = isset($this->json['player']['achievements'][$gamename . "_" . $name])?$this->json['player']['achievements'][$gamename . "_" . $name]:-1;
                while($tier<sizeof($value['tiers']) && $myvalue>=$value['tiers'][$tier]['amount']) {
                    $count += $value['tiers'][$tier]['points'];
                    $tier++;
                }
            }
        }
        return $count;        
    }
    
    public function getNetworkLevel() {
        return number_format(self::getLevel(str_replace(',','',$this->getTotalExp())));
    }

    public function getTotalExp() {
        return number_format($this->json['player']['networkExp'] + self::getTotalExpToLevel((isset($this->json['player']['networkLevel'])?$this->json['player']['networkLevel']:0)+1));
    }

    public function getExpProgress() {
        return number_format(self::getPercentageToNextLevel(str_replace(',','',$this->getTotalExp()))*100,2);
    }
    
    public function getMultiplier() {
        return self::getLevelMultiplier($this->getNetworkLevel());
    }

    public function getFormattedName($withLink = false) {
        if($withLink===true) {
            return '<a target="_blank" href="http://thelagg.com/hypixel/player/' . $this->getUnformattedName() . '">' . $this->getFormattedName(false) . '</a>';
        }
        $plusColor = "#FE3F3F";
        switch($this->json['player']['rankPlusColor']) {
            case 'RED':
                $plusColor = "#FE3F3F";
                break;
            case 'GOLD':
                $plusColor = "#D9A334";
                break;
            case 'GREEN':
                $plusColor = "#3FFE3F";
                break;
            case 'YELLOW':
                $plusColor = "#FEFE3F";
                break;
            case 'LIGHT_PURPLE':
                $plusColor = "#FE3FFE";
                break;
            case 'WHITE':
                $plusColor = "#FFFFFF";
                break;
            case 'BLUE':
                $plusColor = "#3F3FFE";
                break;
            case 'DARK_GREEN':
                $plusColor = "#00BE00";
                break;
            case 'DARK_RED':
                $plusColor = "#BE0000";
                break;
            case 'DARK_AQUA':
                $plusColor = "#00BEBE";
                break;
            case 'DARK_PURPLE':
                $plusColor = "#BE00BE";
                break;
            case 'DARK_GRAY':
                $plusColor = "#3F3F3F";
                break;
            case 'BLACK':
                $plusColor = "#000000";
                break;
            default:
                $plusColor = "#FE3F3F";
                break;
        }
        $rank = isset($this->json['player']['newPackageRank'])?$this->json['player']['newPackageRank']:null;
        if($rank===null) $rank = isset($this->json['player']['packageRank'])?$this->json['player']['packageRank']:null;
        if($rank===null) $rank = 'non';
        if($rank==='NONE') $rank = 'non';
        if(isset($this->json['player']['monthlyPackageRank']) && $this->json['player']['monthlyPackageRank']=='SUPERSTAR') {
            $rank = 'MVP++';
        }
        if(isset($this->json['player']['prefix'])) {
            if($this->json['player']['prefix']==='§c[OWNER]') {
                return '<span style="color:#FE3F3F;">[OWNER] ' . $this->getUnformattedName() . '</span>';
            } else if($this->json['player']['prefix']==='§3[BUILD TEAM]') {
                return '<span style="color:#00BEBE;">[BUILD TEAM] ' . $this->getUnformattedName() . '</span>';
            }
        }
        if(isset($this->json['player']['rank'])) {
            if($this->json['player']['rank']==="ADMIN") {
                return '<span style="color:#FE3F3F;">[ADMIN] ' . $this->getUnformattedName() . '</span>';
            } else if ($this->json['player']['rank']==="MODERATOR") {
                return '<span style="color:#00BE00;">[MOD] ' . $this->getUnformattedName() . '</span>';
            } else if ($this->json['player']['rank']==="HELPER") {
                return '<span style="color:#3F3FFE;">[HELPER] ' . $this->getUnformattedName() . '</span>';
            }  else if ($this->json['player']['rank']==="YOUTUBER") {
                return '<span style="color:#D9A334;">[YOUTUBER] ' . $this->getUnformattedName() . '</span>';
            }
        }
        switch($rank) {
            case 'MVP++':
                
                return '<span data-toggle="tooltip" data-placement="bottom" title="' . $this->getNickname() . '">' .
                    '<span style="color:#D9A334;">[MVP</span>' .
                    '<span style="color:' . $plusColor . ';">++</span>' . 
                    '<span style="color:#D9A334;">] ' . $this->getUnformattedName() . '</span></span>';
            case 'non':
                return '<span style="color:grey;">' . $this->getUnformattedName() . '</span>';
            case 'MVP_PLUS':
                return '<span style="color:aqua;">[MVP</span>' . 
                    '<span style="color:' . $plusColor . ';">+</span>' .
                    '<span style="color:aqua;">] ' . $this->getUnformattedName() . '</span>';
            case 'VIP_PLUS':
                return '<span style="color:green;">[VIP</span>' . 
                    '<span style="color:yellow;">+</span>' .
                    '<span style="color:green;">] ' . $this->getUnformattedName() . '</span>';
            case 'MVP':
                return '<span style="color:aqua;">[MVP] ' . 
                    $this->getUnformattedName() . '</span>';
            case 'VIP':
                return '<span style="color:green;">[VIP] ' . 
                    $this->getUnformattedName() . '</span>';
                default:
                return $this->getUnformattedName() . ' ERROR ' . $rank;
        }
    }
    
    public function getRawData() {
        return $this->json;
    }
    
    public function getNickname() {
        return $this->json['player']['lastNick'];
    }
    
    /**
     * 
     * @return ArcadeResult
     */
    public function getArcadeResult() {return $this->arcadeResult;}
    /**
     * 
     * @return AreanResult
     */
    public function getArenaResult() {return $this->arenaResult;}
    /**
     * 
     * @return WarlordsResult
     */
    public function getWarlordsResult() {return $this->warlordsResult;}
    /**
     * 
     * @return BlitzResult
     */
    public function getBlitzResult() {return $this->blitzResult;}
    /**
     * 
     * @return ComsAndCrimsResult
     */
    public function getCopsAndCrimsResult() {return $this->copsAndCrimsResult;}
    /**
     * 
     * @return PaintballResult
     */
    public function getPaintballResult() {return $this->paintballResult;}
    /**
     * 
     * @return QuakeCraftResult
     */
    public function getQuakeCraftResult() {return $this->quakeCraftResult;}
    /**
     * 
     * @return TntGamesResult
     */
    public function getTntGamesResult() {return $this->tntGamesResult;}
    /**
     * 
     * @return UhcResult
     */
    public function getUhcResult() {return $this->uhcResult;}
    /**
     * 
     * @return VampireZResult
     */
    public function getVampireZResult() {return $this->vampireZResult;}
    /**
     * 
     * @return WallsResult
     */
    public function getWallsResult() {return $this->wallsResult;}
    /**
     * 
     * @return MegaWallsResult
     */
    public function getMegaWallsResult() {return $this->megaWallsResult;}
    /**
     * 
     * @return TurboKartRacersResult
     */
    public function getTurboKartRacersResult() {return $this->turboKartRacersResult;}
    /**
     * 
     * @return SkywarsResult
     */
    public function getSkywarsResult() {return $this->skywarsResult;}
    /**
     * 
     * @return CrazyWallsResult
     */
    public function getCrazyWallsResult() {return $this->crazyWallsResult;}
    /**
     * 
     * @return SmashHeroesResult
     */
    public function getSmashHeroesResult() {return $this->smashHeroesResult;}
    /**
     * 
     * @return SpeedUhcResult
     */
    public function getSpeedUhcResult() {return $this->speedUhcResult;}
    /**
     * 
     * @return SkyclashResult
     */
    public function getSkyclashResult() {return $this->skyclashResult;}
    /**
     * 
     * @return LegacyResult
     */
    public function getLegacyResult() {return $this->legacyResult;}
    /**
     * 
     * @return BedwarsResult
     */
    public function getBedwarsResult() {return $this->bedwarsResult;}
    /**
     * 
     * @return MurderMysteryResult
     */
    public function getMurderMysteryResult() {return $this->murderMysteryResult;}
}
