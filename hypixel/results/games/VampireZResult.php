<?php
/**
 * Description of ArenaResult
 *
 * @author ddude
 */
class VampireZResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
}
