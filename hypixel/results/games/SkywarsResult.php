<?php
/**
 *
 * @author ddude
 */
class SkywarsResult extends Result {
    private static $gamemodes = array(
        'Ranked' => '_ranked',
        'Solo Normal' => '_solo_normal',
        'Solo Insane' => '_solo_insane',
        'Team Normal' => '_team_normal',
        'Team Insane' => '_team_insane',
        'Mega' => '_mega',
        'Overall' => ''
    );
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function draw() {
        ?>
    <div class="row" >
        <div class="col-md-6">
            <?php 
            echo '<br/><b>Coins: </b>' . $this->getCoins();
            echo '<br/><br/><b>Kills: </b>' . $this->getKills('');
            echo '<br/><b>Assists: </b>' . $this->getAssists();
            echo '<br/><b>Deaths: </b>' . $this->getDeaths('');
            echo '<br/><b>K/D Ratio: </b>' . $this->getKDR('');
            echo '<br/><b>Wins: </b>' . $this->getWins('');
            echo '<br/><b>Losses: </b>' . $this->getLosses('');
            echo '<br/><b>W/L Ratio: </b>' . $this->getWLR('');
            echo '<br/><br/><b>Blocks Broken: </b>' . $this->getBlocksBroken();
            echo '<br/><b>Blocks Placed: </b>' . $this->getBlocksPlaced();
            echo '<br/><br/><b>Soul Well Uses: </b>' . $this->getSoulWellUses();
            echo '<br/><b>Soul Well Legendaries: </b>' . $this->getSoulWellLegendaries();
            echo '<br/><b>Soul Well Rares: </b>' . $this->getSoulWellRares();
            echo '<br/><b>Paid Souls: </b>' . $this->getPaidSouls();
            echo '<br/><b>Souls Gathered: </b>' . $this->getSoulsGathered();
            ?>
        </div>
        <div class="col-md-6">
        <?php 
        echo '<br/>';
        Utils::drawTableFromArray($this->getGameTable());
        echo '<br/><b>Eggs Thrown: </b>' . $this->getEggsThrown();
        echo '<br/><b>Enderpearls Thrown: </b>' . $this->getEnderpearlsThrown();
        echo '<br/><b>Arrows Shot: </b>' . $this->getArrowsShot();
        echo '<br/><b>Arrows Hit: </b>' . $this->getArrowsHit();
        echo '<br/><b>Arrow Hit/Miss Ratio: </b>' . $this->getArrowHMR();
        ?>
        </div>
    </div>
        <?php
    }
    
    public function getGameTable() {
        $table = array(
            array('Mode','Kills','Deaths','K/D','Wins','Losses','W/L')
        );
        foreach(self::$gamemodes as $gamemodename => $mode) {
            $row = array(
                $gamemodename,
                $this->getKills($mode),
                $this->getDeaths($mode),
                $this->getKDR($mode),
                $this->getWins($mode),
                $this->getLosses($mode),
                $this->getWLR($mode)
            );
            array_push($table,$row);
        }
        return $table;
    }
    
    public function getCoins() {
        return number_format($this->json['coins']);
    }
    
    private static function convertMode($mode) {
        if(isset(self::$gamemodes[$mode])) {
            return self::$gamemodes[$mode];
        }
        return $mode;
    }
    
    public function getKills($mode) {
        $mode = self::convertMode($mode);
        return number_format($this->json['kills' . $mode]);
    }
    
    public function getDeaths($mode) {
        $mode = self::convertMode($mode);
        return number_format($this->json['deaths' . $mode]);
    }
    
    public function getKDR($mode) {
        return number_format(
                str_replace(',','',$this->getKills($mode))/
                str_replace(',','',$this->getDeaths($mode))                
                ,2);
    }
    
    public function getWins($mode) {
        $mode = self::convertMode($mode);
        return number_format($this->json['wins' . $mode]);
    }
    
    public function getLosses($mode) {
        $mode = self::convertMode($mode);
        return number_format($this->json['losses' . $mode]);
    }
    
    public function getWLR($mode) {
        return number_format(
                str_replace(',','',$this->getWins($mode))/
                str_replace(',','',$this->getLosses($mode))                
                ,2);
    }
    
    public function getAssists() {
        return number_format($this->json['assists']);
    }
    
    public function getBlocksBroken() {
        return number_format($this->json['blocks_broken']);        
    }
    
    public function getBlocksPlaced() {
        return number_format($this->json['blocks_placed']);        
    }
    
    public function getSoulWellUses() {
        return number_format($this->json['soul_well']);
    }
    
    public function getSoulWellLegendaries() {
        return number_format($this->json['soul_well_legendaries']);
    }
    
    public function getSoulWellRares() {
        return number_format($this->json['soul_well_rares']);
    }
    
    public function getPaidSouls() {
        return number_format($this->json['paid_souls']);
    }
    
    public function getSoulsGathered() {
        return number_format($this->json['souls_gathered']);
    }
    
    public function getEggsThrown() {
        return number_format($this->json['egg_thrown']);
    }
    
    public function getEnderpearlsThrown() {
        return number_format($this->json['enderpearls_thrown']);
    }
    
    public function getArrowsShot() {
        return number_format($this->json['arrows_shot']);
    }
    
    public function getArrowsHit() {
        return number_format($this->json['arrows_hit']);
    }
    
    public function getArrowHMR() {
        return number_format($this->json['arrows_hit']/($this->json['arrows_shot']-$this->json['arrows_hit']),2);
    }
}
