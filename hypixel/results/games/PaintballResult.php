<?php
/**
 *
 * @author ddude
 */
class PaintballResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCoins() {
        return number_format($json['coins']);
    }
    
    public function getForceFieldTime() {
        return gmdate('H:i:s',$json['forcefieldTime']);
    }
    
    public function getKills() {
        return number_format($json['kills']);
    }
    
    public function getDeaths() {
        return number_format($json['deaths']);
    }
    
    public function getKillstreaks() {
        return number_format($json['killstreaks']);
    }
    
    public function getShotsFired() {
        return number_format($json['shots_fired']);
    }
    
    public function getWins() {
        return number_format($json['wins']);
    }
    
    public function getKDR() {
        return number_format(
                str_replace(',','',$this->getKills())/
                str_replace(',','',$this->getDeaths())
                ,2);
    }
    
    public function getSKR() {
        return number_format(
                str_replace(',','',$this->getShotsFired())/
                str_replace(',','',$this->getKills())
                ,2);
    }
}
