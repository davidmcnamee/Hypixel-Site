<?php
/**
 * Base reference for object that holds an array, and has sub-results/values 
 * that are derived when constructed
 *
 * @author ddude
 */
abstract class Result {
    
    protected $json;
    
    public function getJson() {
        return $this->json;
    }
}
