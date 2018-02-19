<?php

/**
 *
 * @author ddude
 */
class ArenaResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getGameTable() {
        $table = array(
            array('Type','Kills','Deaths','Wins','Losses','K/D','W/L'),
        );
        foreach(array('1v1','2v2','4v4','Overall') as $type) {
            $array = array($type,
                $this->getKills($type),
                $this->getDeaths($type),
                $this->getWins($type),
                $this->getLosses($type),
                $this->getKDR($type),
                $this->getWLR($type));
            array_push($table,$array);
        }
        return $table;
    }
    
    public function getKills($type = 'Overall') {
        if($type==='1v1') {
            return number_format($this->json['kills_1v1']);
        }
        if($type==='2v2') {
            return number_format($this->json['kills_2v2']);
        }
        if($type==='4v4') {
            return number_format($this->json['kills_4v4']);
        }
        return number_format($this->getKills('1v1') + $this->getKills('2v2') + $this->getKills('4v4'));
    }
    
    public function getDeaths($type = 'Overall') {
        if($type==='1v1') {
            return number_format($this->json['deaths_1v1']);
        }
        if($type==='2v2') {
            return number_format($this->json['deaths_2v2']);
        }
        if($type==='4v4') {
            return number_format($this->json['deaths_4v4']);
        }
        return number_format($this->getDeaths('1v1') + $this->getDeaths('2v2') + $this->getDeaths('4v4'));
    }    
    
    public function getWins($type = 'Overall') {
        if($type==='1v1') {
            return number_format($this->json['wins_1v1']);
        }
        if($type==='2v2') {
            return number_format($this->json['wins_2v2']);
        }
        if($type==='4v4') {
            return number_format($this->json['wins_4v4']);
        }
        return number_format($this->getWins('1v1') + $this->getWins('2v2') + $this->getWins('4v4'));        
    }
    
    public function getLosses($type = 'Overall') {
        if($type==='1v1') {
            return number_format($this->json['losses_1v1']);
        }
        if($type==='2v2') {
            return number_format($this->json['losses_2v2']);
        }
        if($type==='4v4') {
            return number_format($this->json['losses_4v4']);
        }
        return number_format($this->getLosses('1v1') + $this->getLosses('2v2') + $this->getLosses('4v4'));        
    }
    
    public function getKDR($type = 'Overall') {
        return number_format(
                str_replace(',','',$this->getKills($type))/
                str_replace(',','',$this->getDeaths($type))
                ,2);
    }
        
    public function getWLR($type = 'Overall') {
        return number_format(
                str_replace(',','',$this->getWins($type))/
                str_replace(',','',$this->getLosses($type))
                ,2);
    }
}
