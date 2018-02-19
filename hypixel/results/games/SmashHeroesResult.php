<?php
/**
 *
 * @author ddude
 */
class SmashHeroesResult extends Result {
    public static $heroes = array(
        'Bulk' => 'THE_BULK',
        'General Cluck' => 'GENERAL_CLUCK',
        'Spooderman' => 'SPODERMAN',
        'Void Crawler' => 'DUSK_CRAWLER',
        'Tinman' => 'TINMAN',
        'Karakot' => 'GOKU',
        'Pug' => 'PUG',
        'Cryomancer' => 'FROSTY',
        'Cake Monster' => 'CAKE_MONSTER',
        'Botmon' => 'BOTMUN',
        'Skullfire' => 'SKULLFIRE',
        'Marauder' => 'MARAUDER',
        'Sgt. Shield' => 'SERGEANT_SHIELD',
        'Sanic' => 'SANIC'
    );
    
    public static $gamemodes = array(
        '1v1v1v1',
        '2v2',
        '2v2v2',
        'Overall'
    );
    
    private static function isHero($type) {
        return $heroes[$type]!==null;
    }
    
    private static function convertType($type) {
        
    }
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getGameTable() {
        
    }
    
    public function getClassTable() {
        
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getSmashLevel() {
        return number_format($this->json['smash_level_total']);
    }
    
    public function getLevel($class) {
        
    }
    
    public function getPrestige($class) {
        
    }
    
    public function getKills($type) {
        if(self::isHero($type)) {
            
        } else {
            
        }
    }
    
    public function getDeaths($type) {
        if(self::isHero($type)) {
            
        } else {
            
        }        
    }
    
    public function getWins($type) {
        if(self::isHero($type)) {
            
        } else {
            
        }        
    }
    
    public function getLosses($type) {
        if(self::isHero($type)) {
            
        } else {
            
        }        
    }
    
    public function getKDR($type) {
        return number_format(
                str_replace(',','',$this->getKills($type))/
                str_replace(',','',$this->getDeaths($type))
                ,2);
    }
    
    public function getWLR($type) {
        return number_format(
                str_replace(',','',$this->getWins($type))/
                str_replace(',','',$this->getLosses($type))
                ,2);
    }
}
