<?php
/**
 * Description of ArenaResult
 *
 * @author ddude
 */
class TurboKartRacersResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
}
