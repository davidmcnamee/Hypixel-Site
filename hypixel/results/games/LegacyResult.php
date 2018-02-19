<?php
/**
 *
 * @author ddude
 */
class LegacyResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getTotalTokens() {
        return number_format($this->json['tokens']);
    }
    
    public function getPaintballTokens() {
        return number_format($this->json['paintball_tokens']);
    }
    
    public function getWallsTokens() {
        return number_format($this->json['walls_tokens']);
    }
    
    public function getVampireZTOkens() {
        return number_format($this->json['vampirez_tokens']);
    }
    
    public function getQuakeCraftTokens() {
        return number_format($this->json['quakecraft_tokens']);
    }
    
    public function getTurboKartRacersTokens() {
        return number_format($this->json['gingerbread_tokens']);
    }
    
    public function getArenaTokens() {
        return number_format($this->json['arena_tokens']);
    }    
}
