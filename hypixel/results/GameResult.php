<?php

/**
 *
 * @author Ddude88888
 */
class GameResult extends Result {
    
    private $session; //the session at the time the game info was uploaded
    private $player; //the player that uploaded the information for this game
    private $parties; //an array of arrays, which are each a cluster of names
    private $playersInTab; //an array of arrays, which are organized as array(name,team);
    
    public function __construct($array) {
        $this->json = $array;
        try {
            $this->session = new SessionResult($array['session']);
            $this->player = ApiManager::getPlayerResult($array['player'], 3600);
            $this->parties = $array['parties'];
            $this->playersInTab = $array['playersInTab'];
        } catch (Exception $e) {
            throw new Exception('invalid json');
        }
    }

    public function getSession() {
        return $this->session;
    }
    
    public function getPlayer() {
        return $this->player;
    }
}
