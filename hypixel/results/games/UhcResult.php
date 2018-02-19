<?php
/**
 * Description of ArenaResult
 *
 * @author ddude
 */
class UhcResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
}
