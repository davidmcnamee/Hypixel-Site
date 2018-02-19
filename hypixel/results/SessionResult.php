<?php

/**
 *
 * @author Ddude88888
 */
class SessionResult extends Result {
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getUUIDList() {
        return $this->json['session']!==null?$this->json['session']['players']:null;
    }
    
    public function getGameType() {
        
        return $this->json['session']!==null?$this->json['session']['gameType']:null;
    }
    
    public function isSessionNull() {
        return $this->json['session']===null;
    }
}
