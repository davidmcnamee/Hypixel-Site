<?php

/**
 *
 * @author ddude
 */
class MegaWallsResult extends Result{
    public static $classes = array(
        'Hunter' => 'hunter_',
        'Zombie' => 'zombie_',
        'Creeper' => 'creeper_',
        'Arcanist' => 'arcanist_',
        'Shaman' => 'shaman_',
        'Dreadlord' => 'dreadlord_',
        'Golem' => 'golem_',
        'Squid' => 'squid_',
        'Moleman' => 'moleman_',
        'Enderman' => 'enderman_',
        'Herobrine' => 'herobrine_',
        'Blaze' => 'blaze_',
        'Pigman' => 'pigman_',
        'Spider' => 'spider_',
        'Werewolf' => 'werewolf_',
        'Pirate' => 'pirate_',
        'Phoenix' => 'phoenix_',
        'Skeleton' => 'skeleton_'
    );
    
    public static $gamemodes = array(
        'Normal' => '_standard',
        'Faceoff Mode' => '_face_off',
        'Casual Brawl' => '_gvg',
        'Overall' => ''
    );
    
    public function __construct($array) {
        $this->json = $array;
    }   
    
    public function draw() {
        ?>
        <div class="row" >
            <div class="col-md-6">
                <?php
                echo '<p style="padding-bottom: 20px;">';
                echo '<br/><b>Coins: </b>' . $this->getCoins();
                echo '<br/><br/><b>Kills: </b>' . $this->getKills('Overall');
                echo '<br/><b>Assists: </b>' . $this->getAssists('Overall');
                echo '<br/><b>Deaths: </b>' . $this->getDeaths('Overall');
                echo '<br/><b>Final Kills: </b>' . $this->getFinalKills('Overall');
                echo '<br/><b>Final Assists: </b>' . $this->getFinalAssists('Overall');
                echo '<br/><b>Final Deaths: </b>' . $this->getFinalDeaths('Overall');
                echo '<br/><b>Wins: </b>' . $this->getWins('Overall');
                echo '<br/><b>Losses: </b>' . $this->getLosses('Overall');
                echo '<br/><b>K/D Ratio: </b>' . $this->getKDR('Overall');
                echo '<br/><b>Final KDR: </b>' . $this->getFinalKDR('Overall');
                echo '<br/><b>Post-Update Final KDR: </b>' . $this->getPostUpdateFinalKDR();
                echo '<br/><b>Win/Loss Ratio: </b>' . $this->getWLR('Overall');
                echo '<br/><br/><b>Wither Damage: </b>' . $this->getWitherDamage('Overall');
                echo '<br/><b>Defending Kills: </b>' . $this->getDefendingKills('Overall');
                echo '</p>';
                Utils::drawTableFromArray($this->getClassLevelTable());
                ?>
            </div>
            <div class="col-md-6">
                <?php 
                echo '<br/>';
                Utils::drawTableFromArray($this->getClassStatsTable());
                echo '<br/>';
                Utils::drawTableFromArray($this->getGameModeTable()); 
                ?>
            </div>
            </div>
        <?php 
    }
    
    public function getClassStatsTable() {
        $table = array(
            array('Class','FKs','FDs','Wins','Kills','Deaths','FK/FD','K/D')
        );
        foreach(self::$classes as $classname => $class) {
            $row = array(
                $classname,
                $this->getFinalKills($class),
                $this->getFinalDeaths($class),
                $this->getWins($class),
                $this->getKills($class),
                $this->getDeaths($class),
                $this->getFinalKDR($class),
                $this->getKDR($class)
            );
            $allZero = true;
            foreach($row as $index => $thing) {
                if($index===0) {
                    continue;
                }
                if($thing!=='0' && $thing!=='nan') {
                    $allZero = false;
                    break;
                }
            }
            if(!$allZero) {
                array_push($table,$row);
            }
        }
        return $table;
    }
    
    public function getClassLevelTable() {
        $table = array(
            array('Class','Skill','Passive 1','Passive 2','Kit','Gathering', 'EChest', 'Prestige')
        );
        foreach(self::$classes as $classname => $class) {
            $row = array(
                $classname,
                $this->json[$class . 'a'],
                $this->json[$class . 'b'],
                $this->json[$class . 'c'],
                $this->json[$class . 'd'],
                $this->json[$class . 'g'],
                isset($this->json[$class . 'enderchest_level'])?Utils::getRomanNumerals($this->json[$class . 'enderchest_level']):'-',
                isset($this->json[$class . 'prestige_level'])?Utils::getRomanNumerals($this->json[$class . 'prestige_level']):'-'
            );
            $showRow = false;
            foreach($row as $index => $thing) {
                if($index===0) continue;
                if($thing!==null && $thing!=='-') {
                    $showRow = true;
                    break;
                }
            }
            if($showRow) {
                foreach($row as $index => $thing) {
                    if($thing===null) {
                        $row[$index] = 1;
                    }
                    if($index===6 && $thing==='-') {
                        $row[$index] = 'I';
                    }
                }
                array_push($table,$row);
            }
        }
        return $table;
    }
    
    public function getGameModeTable() {
        $table = array(
            array('Mode','Kills','Deaths','K/D','Wins','Losses')
        );
        foreach(self::$gamemodes as $gamemodename => $gamemode) {
            $row = array(
                $gamemodename,
                $this->getKills($gamemode),
                $this->getDeaths($gamemode),
                $this->getKDR($gamemode),
                $this->getWins($gamemode),
                $this->getLosses($gamemode)
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    private static function convertType($type) {
        if(isset(self::$gamemodes[$type])) {
            $type = self::$gamemodes[$type];
        }
        return $type;        
    }
    
    private static function isGameMode($type) {
        foreach(self::$gamemodes as $gamemode) {
            if($type===$gamemode) {
                return true;
            }
        }
        return false;
    }
    
    public function getKills($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['kills' . $type]);
        }
        return number_format($this->json[$type . 'kills']);
    }
    
    public function getDeaths($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['deaths' . $type]);
        }
        return number_format($this->json[$type . 'deaths']);
    }
    
    public function getKDR($type) {
        return number_format(
                str_replace(',','',$this->getKills($type))/
                str_replace(',','',$this->getDeaths($type))
                ,2);
    }
    
    public function getWins($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['wins' . $type]);
        }        
        return number_format($this->json[$type . 'wins']);
    }
    
    public function getLosses($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['losses' . $type]);
        }        
        return number_format($this->json[$type . 'losses']);
    }
    
    public function getAssists($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['assists' . $type]);
        }        
        return number_format($this->json[$type . 'assists']);
    }
    
    public function getPostUpdateFinalKillsInt() {
        return str_replace(',','',$this->getFinalKills('Overall')) - $this->getOldFinalKillsInt();
    }
    
    public function getOldFinalKillsInt() {
        return isset($this->json['finalKills'])?$this->json['finalKills']:0;
    }
    
    public function getFinalKills($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['final_kills' . $type]);
        }        
        return number_format($this->json[$type . 'final_kills']);
    }
    
    public function getFinalAssists($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['final_assists' . $type]);
        }        
        return number_format($this->json[$type . 'final_assists']);
    }
    
    public function getPostUpdateFinalDeathsInt() {
        return isset($this->json['final_deaths'])?$this->json['final_deaths']:0;
    }
    
    public function getPostUpdateFinalKDR() {
        return number_format($this->getPostUpdateFinalKillsInt()/$this->getPostUpdateFinalDeathsInt(),2);
    }
    
    public function getFinalDeaths($type) {
        $type = self::convertType($type);
        if(trim($type)==='') {
            return number_format($this->json['final_deaths'] + $this->json['finalDeaths']);
        }
        if(self::isGameMode($type)) {
            return number_format($this->json['final_deaths' . $type]);
        }        
        return number_format($this->json[$type . 'final_deaths']);
    }
    
    public function getFinalKDR($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format(
                    str_replace(',','',$this->getFinalKills($type))/
                    str_replace(',','',$this->getFinalDeaths($type))
                    ,2);
        }        
        return number_format(
                $this->json[$type . 'final_kills_melee']/
                str_replace(',','',$this->getFinalDeaths($type))
                ,2);
    }
    
    public function getWLR($type) {
        return number_format(
                str_replace(',','',$this->getWins($type))/
                str_replace(',','',$this->getLosses($type))
                ,2);
    }
    
    public function getWitherDamage($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['wither_damage' . $type]);
        }
        return number_format($this->json[$type . 'wither_damage']);        
    }
    
    public function getDefendingKills($type) {
        $type = self::convertType($type);
        if(self::isGameMode($type)) {
            return number_format($this->json['defender_kills' . $type]);
        }
        return number_format($this->json[$type . 'defender_kills']);        
    }
    
    public function getSelectedClass() {
        return $this->json['chosen_class'];
    }
}
