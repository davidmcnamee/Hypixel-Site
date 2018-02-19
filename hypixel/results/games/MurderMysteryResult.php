<?php
/**
 *
 * @author ddude
 */
class MurderMysteryResult extends Result {
    private static $gamemodes = array(
        'Overall' => '',
        'Classic' => '_MURDER_CLASSIC',
        'Hardcore' => '_MURDER_HARDCORE',
        'Assassins' => '_MURDER_ASSASSINS'
    );
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getGameTable() {
        $table = array(
            array('Mode','Kills','Bow Kills','Knife Kills','Thrown Knife Kills')
        );
        foreach(self::$gamemodes as $gamemodename => $gamemode) {
            $row = array(
                $gamemodename,
                $this->getKills($gamemode),
                $this->getBowKills($gamemode),
                $this->getKnifeKills($gamemode),
                $this->getThrownKnifeKills($gamemode)
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    public function convertMode($mode) {
        if(!ctype_upper($mode)) {
            return $gamemodes[$mode];
        }
        return $mode;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getKills($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['kills' . $mode]);
    }
    
    public function getBowKills($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['bow_kills' . $mode]);
    }
    
    public function getKnifeKills($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['knife_kills' . $mode]);
    }
    
    public function getThrownKnifeKills($mode) {
        $mode = convertMode($mode);
        return number_format($this->json['thrown_knife_kills' . $mode]);
    }
}
