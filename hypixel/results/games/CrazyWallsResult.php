<?php

/**
 *
 * @author ddude
 */
class CrazyWallsResult extends Result {
    private static $modes = array(
        'Solo Normal' => '',
        'Solo Lucky' => '_solo_chaos',
        'Team Normal' => '_team',
        'Team Lucky' => '_team_chaos',
        'Overall' => 'overall'
    );
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getGameTable() {
        $table = array(
            array('Mode','Kills','Deaths','Wins','K/D')
        );
        foreach($this->modes as $modename => $mode) {
            $row = array(
                $modename,
                $this->getKills($mode),
                $this->getDeaths($mode),
                $this->getWins($mode),
                $this->getKDR($mode)
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getKills($mode) {
        if($mode==='overall' || $mode==='Overall') {
            $count = 0;
            foreach($this->modes as $submode) {
                if($submode!=='overall') {
                    $count += str_replace(',','',$this->getKills($submode));
                }
            }
            return $count;
        } else {
            if(!ctype_lower($mode)) {
                $mode = $this->modes[$mode];
            }
            return number_format(($mode===''?'':'crazywalls_') . 'kills' . $mode);
        }
    }
    
    public function getDeaths($mode) {
        if($mode==='overall' || $mode==='Overall') {
            $count = 0;
            foreach($this->modes as $submode) {
                if($submode!=='overall') {
                    $count += str_replace(',','',$this->getDeaths($submode));
                }
            }
            return $count;
        } else {
            if(!ctype_lower($mode)) {
                $mode = $this->modes[$mode];
            }
            return number_format(($mode===''?'':'crazywalls_') . 'deaths' . $mode);
        }        
    }
    
    public function getWins($mode) {
        if($mode==='overall' || $mode==='Overall') {
            $count = 0;
            foreach($this->modes as $submode) {
                if($submode!=='overall') {
                    $count += str_replace(',','',$this->getWins($submode));
                }
            }
            return number_format($count);
        } else {
            if(!ctype_lower($mode)) {
                $mode = $this->modes[$mode];
            }
            return number_format(($mode===''?'':'crazywalls_') . 'wins' . $mode);
        }        
    }
            
    public function getKDR($mode) {
        return number_format(str_replace(',','',$this->getKills($mode)) / str_replace(',','',$this->getKills($mode)),2);
    }
}
