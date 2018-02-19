<?php

/**
 * Description of NameToUUIDResult
 *
 * @author Ddude88888
 */
class NameToUUIDResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getUUID() {
        return $this->json['id'];
    }
    
    public function isSuccesful() {
        if($this->getUUID()!==null) {
            return true;
        }
        return false;
    }
}
