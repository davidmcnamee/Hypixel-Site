<?php
/**
 *
 * @author ddude
 */
class SkyclashResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getClassTable() {
        $table = array(
            array('Kit','Card 1','Card 2','Card 3')
        );
        for($i = 0; $this->json['class_' . $i]!==null; $i++) {
            $temp = explode($this->json['class_' . $i],';');
            $kit = ucwords($temp[0]) . ' ' . Utils::getRomanNumerals($this->json['kit_' . strtolower($temp[0]) . '_minor']);
            $row = array($kit);
            for($j = 1; $j<=3; $j++) {
                $card = ucwords(str_replace('_',' ',$temp[$j])) . ' ' . Utils::getRomanNumerals($this->json['perk_' . strtolower($temp[$j])]);
                array_push($row,$card);
            }
            array_push($table,$row);
        }
        return $table;
    }
    
    public function getKitTable() { //TODO
        $table = array(
            array('Kit','Wins','Kills','Assists','Deaths','K/D')
        );
        return $table;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getWins() {
        return number_format($this->json['wins']);
    }
    
    public function getKills() {
        return number_format($this->json['kills']);
    }
    
    public function getAssists() {
        return number_format($this->json['assists']);
    }
    
    public function getDeaths() {
        return number_format($this->json['deaths']);
    }
    
    public function getKDR() {
        return number_format(
                str_replace(',','',getKills())/
                str_replace(',','',getDeaths())
                ,2);
    }
}
