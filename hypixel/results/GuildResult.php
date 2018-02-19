<?php
/**
 * Description of GuildResult
 *
 * @author ddude
 */
class GuildResult extends Result {
    
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getMembers() {
        return $this->json['guild']['members'];
    }
    
    public function getName() {
        return $this->json['guild']['name'];
    }
    
    public function isSuccessful() {
        return $this->json['success']===true && $this->json['guild']!==null;
    }
}
