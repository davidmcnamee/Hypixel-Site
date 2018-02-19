<?php

/**
 *
 * @author ddude
 */
class BlitzResult extends Result {
    public static $kits = array(
        'Arachnologist' => 'arachnologist',
        'Archer' => 'archer',
        'Armorer' => 'armorer',
        'Astronaut' => 'astronaut',
        'Baker' => 'baker',
        'Blaze' => 'blaze',
        'Creepertamer' => 'creepertamer',
        'Horsetamer' => 'horsetamer',
        'Hunter' => 'hunter',
        'Knight' => 'knight',
        'Necromancer' => 'necromancer',
        'Pigman' => 'pigman',
        'RedDragon' => 'reddragon',
        'Rogue' => 'rogue',
        'Scout' => 'scout',
        'SlimeySlime' => 'slimeyslime',
        'Speleologist' =>' speleologist',
        'Wolftamer' => 'wolftamer',
        'Paladin' => 'paladin',
        'Fisherman' => 'fisherman'
    );
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function draw() {
        ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo '<br/><b>Coins: </b>' . $this->getCoins();
            echo '<br/><br/><b>Kills: </b>' . $this->getKills();
            echo '<br/><b>Deaths: </b>' . $this->getDeaths();
            echo '<br/><b>Wins Solo: </b>' . $this->getWinsSolo();
            echo '<br/><b>Wins Team: </b>' . $this->getWinsTeams();
            echo '<br/><b>K/D Ratio: </b>' . $this->getKDR();
            echo '<br/><b>Kills/Game: </b>' . $this->getKillsPerGame();
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo '<br/>';
            Utils::drawTableFromArray($this->getKitTable());
            ?>
        </div>
    </div>
    <?php
    }
    
    public function getKitTable() {
        $table = array(array('Kit','Level'));
        foreach(self::$kits as $name => $kit) {
            $row = array(
                $name,
                Utils::getRomanNumerals($this->getKitLevel($kit))
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    public function getKitLevel($kitName) {
        $kitName = trim($kitName);
        if(!isset(self::$kits[$kitName])) {
            return number_format($this->json[$kitName] + 1);
        }
        $index = isset(self::$kits[$kitName])?self::$kits[$kitName]:$kitName;
        return number_format((isset($this->json[$index])?$this->json[$index]:0) + 1);
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getKills() {
        return number_format($this->json['kills']);        
    }
    
    public function getDeaths() {
        return number_format($this->json['deaths']);        
    }
    
    public function getWinsSolo() {
        return number_format($this->json['wins']);        
    }
    
    public function getWinsTeams() {
        return number_format($this->json['wins_teams']);
    }
    
    public function getKDR() {
        return number_format(
            str_replace(',','',$this->getKills())/
            str_replace(',','',$this->getDeaths())
                ,2);
    }
    
    public function getKillsPerGame() {
            return number_format(
            str_replace(',','',$this->getKills())/
            (str_replace(',','',$this->getWinsSolo())
            + str_replace(',','',$this->getDeaths()))
                ,2);        
    }
}
