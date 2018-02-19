<?php

/**
 * Description of PlayerPage
 *
 * @author Ddude88888
 */
class PlayerPage extends Page {
    /**
     *
     * @var PlayerResult
     */
    private $playerResults;
    private $alreadyLoaded;
    
    public function __construct($playerResults, $alreadyLoaded) {
        $this->alreadyLoaded = $alreadyLoaded;
        $this->playerResults = $playerResults;
    }
    
    /**
     * 
     * @param PlayerResult $player
     * @param bool $expanded
     */
    public function drawPlayerModule($player, $expanded) {
?>
<div class="container" style="border-radius: 25px; padding-right: 30px; padding-left: 30px; margin-top: 35px; padding-bottom: 20px; border:1px solid black;"><br/>
    <h3><a href="<?php echo("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . '?refresh=1'); ?>"><?php echo $player->getFormattedName(); ?></a></h3>
        <div id="accordion" data-children=".item">
            <button aria-controls="#General-<?php echo($player->getUnformattedName()); ?>" href="#General-<?php echo($player->getUnformattedName()); ?>" type="button" class="btn-sm btn btn-outline-dark" data-toggle="collapse" data-parent="#accordion" aria-expanded="true">
                General
            </button>
            <button aria-controls="#MegaWalls-<?php echo($player->getUnformattedName()); ?>" href="#MegaWalls-<?php echo($player->getUnformattedName()); ?>" type="button" class="btn-sm btn btn-outline-dark" data-toggle="collapse" data-parent="#accordion" aria-expanded="true">
                MegaWalls
            </button>
            <button aria-controls="#Skywars-<?php echo($player->getUnformattedName()); ?>" href="#Skywars-<?php echo($player->getUnformattedName()); ?>" type="button" class="btn-sm btn btn-outline-dark" data-toggle="collapse" data-parent="#accordion" aria-expanded="true">
                Skywars
            </button>
            <button aria-controls="#BSG-<?php echo($player->getUnformattedName()); ?>" href="#BSG-<?php echo($player->getUnformattedName()); ?>" type="button" class="btn-sm btn btn-outline-dark" data-toggle="collapse" data-parent="#accordion" aria-expanded="true">
                BSG
            </button>
            <button aria-controls="#API-<?php echo($player->getUnformattedName()); ?>" href="#API-<?php echo($player->getUnformattedName()); ?>" type="button" class="btn-sm btn btn-outline-dark" data-toggle="collapse" data-parent="#accordion" aria-expanded="true">
                API
            </button>
            <div class="item">
            <div id="General-<?php echo($player->getUnformattedName()); ?>" class="collapse<?php if($expanded) echo(' show');?>" role="tabpanel">
                <?php $player->draw(); ?>
            </div>
            </div>
            <div class="item">
            <div id="MegaWalls-<?php echo($player->getUnformattedName()); ?>" class="collapse" role="tabpanel">
                <?php $player->getMegaWallsResult()->draw(); ?>
            </div>
            </div>
            <div class="item">
            <div id="Skywars-<?php echo($player->getUnformattedName()); ?>" class="collapse" role="tabpanel">
                <?php $player->getSkywarsResult()->draw(); ?>
            </div>
            </div>
            <div class="item">
            <div id="BSG-<?php echo($player->getUnformattedName()); ?>" class="collapse" role="tabpanel">
                <?php $player->getBlitzResult()->draw(); ?>
            </div>
            </div>
            <div class="item">
            <div id="API-<?php echo($player->getUnformattedName()); ?>" class="collapse" role="tabpanel">
                <?php 
                $raw = $player->getRawData();
                unset($raw['timeRequested']);
                self::printArrayRecursive($raw, 0); 
                ?>
            </div>
            </div>
        </div>
        </div>
<?php
    }
    
    public function drawPage() {
        ?>
<html>
    <head>
        <title><?php echo $this->playerResults[0]->getUnformattedName(); ?> - Stats</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <style>
            html {
                margin-right: calc(100% - 100vw);
            }
            body {
                margin-right: calc(100vw - 100%);
            }
        </style>
        <script>
            function loadPlayers() {
                    var url = "/hypixel/pages/updatePlayersWithName.php?name=<?php echo $this->playerResults[0]->getUnformattedName(); ?>";
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("mainContainer").innerHTML += this.responseText;
                            document.getElementById("refreshWarningMsg").innerHTML = "";
                        }
                    }
                    xmlhttp.open("GET", url, true);
                    xmlhttp.send();
            }
        </script>
    </head>
    <body <?php if($this->alreadyLoaded) {} else { echo 'onload="loadPlayers()"'; } ?>>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="">
                <img src="http://thelagg.com/pika.png" width="40" height="40" alt="">
            </a>
            <a class="navbar-brand" href="">Lagg</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-items">
                        <a class="nav-link" href="http://thelagg.com">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://thelagg.com/hypixel">Hypixel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://thelagg.com/projects">Projects</a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php 
        
        if($this->alreadyLoaded==false) {
            echo '<p id="refreshWarningMsg">Currently loading all other players with this name...</p>';
        }
        $show = true;
        ?>
        <div id="mainContainer">
        <?php
        foreach($this->playerResults as $playerResult) {
            $this->drawPlayerModule($playerResult,$show); 
            $show = false;
        }
        ?>
        </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    </body>
    
</html>
            <?php
    }

    public static function printArrayRecursive($array, $depth, $idprefix = '') {
        $count = 1;
        foreach($array as $key => $entry) { 
            if(is_array($entry)) {
                $id = $idprefix . $key . $count;
                $count++;
                echo "<a class=\"btn btn-primary\" data-toggle=\"collapse\" href=\"#$id\">$key</a>";
                echo "<div class=\"" . ($depth===0||is_int($key)?"collapse show":"collapse") . "\" id=\"" . $id . "\"><div class=\"card card-body\">";
                self::printArrayRecursive($entry,$depth+1,$idprefix . $key);
                echo "</div></div>";
            } else {
                echo $key . ": " . $entry . "<br/>";
            }
        }
    }
    
}
