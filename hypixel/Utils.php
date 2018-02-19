<?php
class Utils {
    /**
     * returns the timestamp in milliseconds
     * @return int 
     */
    public static function get_millisecond() {
        list($msec, $sec) = explode(' ', microtime());
        $time_milli = (int) $sec.substr($msec, 2, 3); // '1491536422147'
        return $time_milli;
    }
    
    public static function formatUUID($input) {
        if(strlen($input)===32) {
            $input = substr($input,0,8) . '-' . substr($input,8,4) . '-' . substr($input,12,4) . '-' . substr($input,16,4) . '-' . substr($input,20,12);
        }
        return $input;
    }
    
    public static function getRomanNumerals($integer) {
        if($integer===0 || $integer===null) {
            return '-';
        }
        $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
        $return = ''; 
        while($integer > 0) 
        { 
            foreach($table as $rom=>$arb) 
            { 
                if($integer >= $arb) 
                { 
                    $integer -= $arb; 
                    $return .= $rom; 
                    break; 
                } 
            } 
        } 
        return $return;
    }
    
    public static function drawTableFromArray($array) {
    ?>
    <div class="table-responsive fixed-table-body">
    <table class="table table-hover table-bordered table-sm" >
        <thead class="thead-dark">
            <tr>
    <?php 
    foreach($array[0] as $head) {
        echo "<th>$head</th>";
    }
    ?>
            </tr>
        </thead>
        <tbody>
    <?php 
    foreach($array as $index => $row) {
        if($index===0) {
            continue;
        }
        echo '<tr>';
        foreach($row as $statvalue) {
            if(is_numeric(str_replace(',','',$statvalue)) || $statvalue==='nan') {
                echo '<td style="text-align:right;">' . ($statvalue==='nan'?'∞':$statvalue) . '</td>';
            } else {
                echo '<td style="text-align:left;">' . $statvalue . '</td>';
            }
        }
        echo "</tr>";
    }
    ?>
        </tbody>
    </table>  
    </div>
    <?php
    }
}