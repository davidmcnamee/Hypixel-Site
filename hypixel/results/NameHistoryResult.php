<?php

/**
 *
 * @author Ddude88888
 */
class NameHistoryResult extends Result {
    public function __construct($array) {
        $this->json = $array;
    }
    
    public function getCurrentName() {
        if($this->json[0]===null) {
            return null;
        }
        $index = (count($this->json)-2);
        return $this->json[$index]['name'];
    }
    
    /**
     * 
     * @param type $name
     * @return long time in seconds
     */
    public function getTimeOfName($name) {
        foreach($this->json as $value) {
            if(!isset($value['name'])) return -1;
            if(strtolower($name)== strtolower($value['name']) && isset($value['changedToAt'])) {
                return $value['changedToAt']/1000;
            }
        }
        return -1;
    }
    
    public function printNameHistory() {
        $array = array();
        $json2 = array_reverse($this->json);
        $isCurrent = true;
        foreach($json2 as $name) {
            if($name['name']===null) {
                continue;
            }
            if(!isset($name['changedToAt'])) {
                echo(($isCurrent===true?'<br/>':'<hr/>') . '<b>' . $name['name'] . '</b> (original)' . ($isCurrent===true?' (current)':''));
                if($isCurrent===true) {
                    $isCurrent = false;
                }
            } else {
                echo(($isCurrent===true?'<br/>':'<hr/>') . '<b>' . $name['name'] . '</b> ' . date('d/m/Y @ g:ia',$name['changedToAt']/1000) . ($isCurrent===true?' (current)':''));
                if($isCurrent===true) {
                    $isCurrent = false;
                }
            }
        }
        return $array;
    }
}
