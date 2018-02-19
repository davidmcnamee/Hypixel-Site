<?php
/**
 * Description of ArenaResult
 *
 * @author ddude
 */
class WallsResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
}
