<?php
/**
 * Description of ArenaResult
 *
 * @author ddude
 */
class TntGamesResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
}
