<?php

/**
 *
 * @author ddude
 */
class CopsAndCrimsResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getTotalKills() {
        return number_format($this->json['kills'] + $json['kills_deathmatch']);
    }
    
    public function getHeadshots() {
        return number_format($this->json['headshot_kills']);
    }
    
    public function getDeaths() {
        return number_format($this->json['deaths']);
    }
    
    public function getGameWins() {
        return number_format($this->json['wins']);
    }
    
    public function getRoundWins() {
        return number_format($this->json['round_wins']);        
    }
    
    public function getShotsFired() {
        return number_format($this->json['shots_fired']);        
    }
    
    public function getCopKills() {
        return number_format($this->json['cop_kills']);        
    }
    
    public function getCriminalKills() {
        return number_format($this->json['criminal_kills']);
    }
    
    public function getDeathmatchKills() {
        return number_format($this->json['kills_deathmatch']);
    }
    
    public function getBombsPlanted() {
        return number_format($this->json['bombs_planted']);
    }
    
    public function getBombsDefused() {
        return number_format($this->json['bombs_defused']);
    }
}
