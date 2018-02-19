<?php
/**
 *
 * @author ddude
 */
class QuakeCraftResult extends Result {
    public static $gamemodes = array(
        'Solo' => '',
        'Team' => '_teams'
    );
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getGameTable() {
        $table = array(
            array('Mode','Kills','Deaths','Wins','K/D','Headshots','Killstreaks','H/K','Kills/Shot')
        );
        foreach(self::$gamemodes as $gamemodename => $gamemode) {
            $row = array(
                $gamemodename,
                $this->getKills($gamemode),
                $this->getDeaths($gamemode),
                $this->getWins($gamemode),
                $this->getKDR($gamemode),
                $this->getHeadshots($gamemode),
                $this->getKillstreaks($gamemode),
                $this->getHKR($gamemode),
                $this->getKSR($gamemode)
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    private static function convertMode($mode) {
        if(!ctype_lower($mode)) {
            return self::$gamemodes[$mode];
        }
        return $mode;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getHighestKillstreak() {
        return number_format($this->json['highest_killstreak']);
    }
    
    public function getDashPower() {
        return number_format($this->json['dash_power']);
    }
    
    public function getDashCooldown() {
        return number_format($this->json['dash_cooldown']);
    }
    
    public function getKills($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['kills' . $mode]);
    }
    
    public function getDeaths($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['deaths' . $mode]);        
    }
    
    public function getWins($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['wins' . $mode]);        
    }
    
    public function getKDR($mode) {
        return number_format(
                str_replace(',','',$this->getKills($mode))/
                str_replace(',','',$this->getDeaths($mode))
                ,2);
    }
    
    public function getHeadshots($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['kills' . $mode]);        
    }
    
    public function getKillstreaks($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['kills' . $mode]);        
    }
    
    public function getHKR($mode) {
        $mode = convertMode($mode);
        return number_format(
                str_replace(',','',$this->getHeadshots($mode))/
                str_replace(',','',$this->getKills($mode))
                ,2);     
    }
    
    public function getKSR($mode) {
        return number_format(
                str_replace(',','',$this->getKills($mode))/
                $this->json['shots_fired' . $mode]
                ,2);       
    }
}
