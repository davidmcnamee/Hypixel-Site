<?php

/**
 *
 * @author ddude
 */
class ArcadeResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCoins() {
        return $this->json['coins'];
    }
    
    public function getKillsBlockingDead() {
        return number_format($this->json['kills_dayone']);
    }
    
    public function getKillsDragonWars() {
        return number_format($this->json['kills_dragonwars2']);
    }
    
    public function getKillsBountyHunter() {
        return number_format($this->json['kills_oneinthequiver']);
    }
    
    public function getKillsThrowOut() {
        return number_format($this->json['kills_throw_out']);
    }
    
    public function getPoopCollected() {
        return number_format($this->json['poop_collected']);
    }
    
    public function getHeadshotsBlockingDead() {
        return number_format($this->json['headshots_dayone']);
    }
    
    public function getWinsBlockingDead() {
        return number_format($this->json['wins_dayone']);
    }
    
    public function getWinsDragonwars() {
        return number_format($this->json['wins_dragonwars2']);
    }
    
    public function getWinsEnderspleef() {
        return number_format($this->json['wins_ender']);
    }
    
    public function getWinsFarmHunt() {
        return number_format($this->json['wins_farm_hunt']);
    }
    
    public function getWinsBountyHunter() {
        return number_format($this->json['wins_oneinthequiver']);
    }
    
    public function getWinsPartyGames1() {
        return number_format($this->json['wins_party']);
    }
    
    public function getWinsPartyGames2() {
        return number_format($this->json['wins_party_2']);
    }
    
    public function getWinsPartyGames3() {
        return number_format($this->json['wins_party_3']);
    }
    
    public function getWinsThrowOut() {
        return number_format($this->json['wins_throw_out']);
    }
    
    public function getWinsHoleInTheWall() {
        return number_format($this->json['wins_hole_in_the_wall']);
    }
    
    public function getHITWHighestScoreQualifications() {
        return number_format($this->json['hitw_record_q']);
    }
    
    public function getHITWHighestScoreFinals() {
        return number_format($this->json['hitw_record_f']);
    }
    
    public function getWinsHypixelSays() {
        return number_format($this->json['wins_simon_says']);
    }
    
    public function getWinsMiniWalls() {
        return number_format($this->json['wins_mini_walls']);
    }
    
    public function getGWKills() {
        return number_format($this->json['sw_kills']);
    }
    
    public function getGWEmpireKills() {
        return number_format($this->json['sw_empire_kills']);
    }
    
    public function getGWRebelKills() {
        return number_format($this->json['sw_rebel_kills']);
    }
    
    public function getGWDeaths() {
        return number_format($this->json['sw_deaths']);
    }
    
    public function getGWShotsFired() {
        return number_format($this->json['sw_shots_fired']);
    }
}
