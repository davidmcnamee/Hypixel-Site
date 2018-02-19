<?php

/**
 *
 * @author ddude
 */
class BedwarsResult extends Result {
    private static $types = array(
        'Solo' => 'eight_one_',
        'Doubles' => 'eight_two_',
        '3v3v3v3' => 'four_three_',
        '4v4v4v4' => 'four_four_',
        'Overall' => '');
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function draw() {
        ?>
    <div class="row" >
<?php 
            echo '<br/><b>Coins: </b>' . $this->getCoins();
            echo '<br/><br/><b>Current Winstreak: </b>' . $this->getCurrentWinstreak();
            echo '<br/><b>Current Level: </b>' . $this->getCurrentLevel();
            Utils::drawTableFromArray($this->getGameTable());
?>        
    </div>
        <?php
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    public function getCurrentWinstreak() {
        return number_format($this->json['winstreak']);
    }
    
    public static function getBedwarsLevel($exp) {
        // first few levels are different
        if ($exp < 500) {
            return 0;
        } else if ($exp < 1500) {
            return 1;
        } else if ($exp < 3500) {
            return 2;
        } else if ($exp < 5500) {
            return 3;
        } else if ($exp < 9000) {
            return 4;
        }
        $exp -= 9000;
        return number_format($exp / 5000 + 4,2);
    }
    
    public function getCurrentLevel() {
        return getBedwarsLevel($this->json['Experience']);
    }
    
    public function getGameTable() {
        $table = array(
            array('Type','Kills','Deaths','Final Kills','Final Deaths','Wins','Losses','Beds Broken','K/D','Final K/D','W/L')
        );
        foreach($this->types as $type  => $value) {
            $row = array(
                $type,
                $this->getKills($type),
                $this->getDeaths($type),
                $this->getFinalKills($type),
                $this->getWins($type),
                $this->getLosses($type),
                $this->getBedsBroken($type),
                $this->getKDR($type),
                $this->getFinalKDR($type),
                $this->getWLR($type)
            );
            array_push($table,$row);
        }  
    }
    
    public function getKills($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'kills_bedwars'
            ]);
    }
    
    public function getDeaths($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'deaths_bedwars'
            ]);
    }
    
    public function getFinalKills($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'final_kills_bedwars'
            ]);        
    }
    
    public function getFinalDeaths($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'final_deaths_bedwars'
            ]);        
    }
    
    public function getWins($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'wins_bedwars'
            ]);        
    }
    
    public function getLosses($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'losses_bedwars'
            ]);        
    }
    
    public function getBedsBroken($type) {
        return number_format(
            $this->json[
                $this->types[$type] . 
                    'beds_broken_bedwars'
            ]);        
    }
    
    public function getKDR($type) {
        return number_format(
                str_replace(',','',$this->getKills($type))/
                str_replace(',','',$this->getDeaths($type))
                ,2);
    }
    
    public function getFinalKDR($type) {
        return number_format(
                str_replace(',','',$this->getFinalKills($type))/
                str_replace(',','',$this->getFinalDeaths($type))
                ,2);        
    }
    
    public function getWLR($type) {
        return number_format(
                str_replace(',','',$this->getWins($type))/
                str_replace(',','',$this->getLosses($type))
                ,2);        
    }
}
