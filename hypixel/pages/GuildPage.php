<?php

/**
 * Description of GuildPage
 *
 * @author Ddude88888
 */
class GuildPage extends Page {
    /**
     *
     * @var GuildResult 
     */
    private $guildResult;
    
    public function __construct($guildResult) {
        $this->guildResult = $guildResult;
    }
    
    public function drawPage($extraValues = []) {
        
        ?>
<html>
    <head>
        <title>
            <?php echo $this->guildResult->getName(); ?>
        </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <link rel="icon" type="image/png" href="http://heyitsdavid.com/favicon-32x32.png" sizes="32x32" />    
        <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js?config=TeX-MML-AM_CHTML"></script>
        <script src="dragtable.js"></script>
        <script src="sorttable.js"></script>
        <script>
            function loadStats() {
                var rows = document.getElementsByTagName("table")[0].rows;
                for(var i = 1; i<rows.length; i++) {
                    var row = rows[i];
                    var temparray = window.location.href.toString().split("/");
                    var stats = temparray[temparray.length-1];
                    var uuid = row.cells[0].innerHTML;
                    var url = "/hypixel/pages/getPlayerStats.php?uuid=" + uuid + "&stats=" + stats;
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var arr = this.responseText.split("--");
                            var rows2 = document.getElementsByTagName("table")[0].rows;
                            for(var j = 1; j<rows2.length; j++) {
                                var row2 = rows[j];
                                var uuid2 = row2.cells[0].innerHTML;
                                if(uuid2 == arr[0]) {
                                    row2.innerHTML = arr[1];
                                }
                            }
                        }
                    };
                    xmlhttp.open("GET", url, true);
                    xmlhttp.send();
                }
            }
        </script>
    </head>
    <?php

        $extraValues = SessionPage::fixExtraValues($extraValues);
        
        $table = array();
        foreach($extraValues as $id) {
            $table[0][Draw::$StatRequests[$id][0]] = Draw::$StatRequests[$id][1];
        }
        
        foreach($this->guildResult->getMembers() as $member) {
            $uuid = $member['uuid'];
            array_push($table,array($uuid));
        }

?>

<body style="background-color:F8F8F8;" onload="loadStats()">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="">
                <img src="http://heyitsdavid.com/pika.png" width="40" height="40" alt="">
            </a>
            <a class="navbar-brand" href=""><?php
                if(strpos($_SERVER['REQUEST_URI'], 'hypixel') !== false) {
                    echo "Lagg";
                } else {
                    echo "David M";
                }
            ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-items">
                        <a class="nav-link" href="http://heyitsdavid.com">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://heyitsdavid.com/hypixel">Hypixel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://heyitsdavid.com/projects">Projects</a>
                    </li>
                </ul>
            </div>
        </nav>

<div class="container">

    <h2 style="padding-top: 20px; padding-bottom: 20px"><?php echo $this->guildResult->getName(); ?></h2>  
        <div class="dropdown">
        <button class="btn btn-outline-dark dropdown-toggle" type="button" data-toggle="dropdown">
            Add Stat Category
            <span class="caret"></span></button>
        <ul class="dropdown-menu">
            <?php
            $url = '/' . trim($_SERVER['REQUEST_URI'],'/');
            if(substr_count($url,'/')!==4) {
                $url = $url . '/';
            }
            foreach(Draw::$StatRequests as $id => $stat) {
                if(array_search($id,$extraValues)===false) {
                    echo '<li style="padding-left: 5px;"><a style="color: inherit;" href="' . $url . '.' . $id . '">' . $stat[5] . '</a></li>';
                }
            }
            ?>
        </ul>
    </div>
  <table class="draggable sortable table table-hover" id="myTable">
    <thead>
      <tr>
        <?php 
        $count = 0;
        foreach($table[0] as $key => $value) {
            $temp = explode('/',trim($_SERVER['REQUEST_URI'],'/'));
            $statid = 0;
            foreach(Draw::$StatRequests as $id => $statreq) {
                if($key===$statreq[0]) {
                    $statid = $id;
                }
            }
            $temp[3] = str_replace($statid,'',$temp[3]);
            $removeurl = implode('/',$temp);
            echo "<th style=\"text-align:center; vertical-align:middle;\"><a onclick=\"sortTable($count)\" style=\"cursor: pointer;\">$key</a>"
                    . "<button type=\"button\" class=\"close\" aria-label=\"Remove Stat\"><a href=\"/$removeurl\" style=\"text-decoration: none; color: inherit;\"><span aria-hidden=\"true\">&times;</span></a></button></th>";
            $count++;
        }
        $tableheader = array_values($table[0]);

        array_shift($table);
        ?>
      </tr>
    </thead>
    <tbody>
      <?php 
      foreach($table as $row) {
        echo '<tr>'; 
        echo '<td>' . $row[0] . '</td>';
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>
<script>
function isNumeric(n) {
  return !isNaN(n) || n==="";
}

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("myTable");
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.getElementsByTagName("TR");
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc") {
        if(isNumeric(x.innerHTML.replace(/,/g, "")) && isNumeric(y.innerHTML.replace(/,/g, ""))) {
          if(Number(parseFloat(x.innerHTML.replace(/,/g, ""))) > Number(parseFloat(y.innerHTML.replace(/,/g, "")))) {
            shouldSwitch= true;
            break;
          }
        } else if (x.innerHTML=="N/A" || y.innerHTML=="N/A") {
            if(x.innerHTML=="N/A" && y.innerHTML!="N/A") {
                shouldSwitch = true;
                break;
            }
        } else {
          if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
            shouldSwitch= true;
            break;
          }
        }
      } else if (dir == "desc") {
        if(isNumeric(x.innerHTML.replace(/,/g, "")) && isNumeric(y.innerHTML.replace(/,/g, ""))) {
          if(Number(parseFloat(x.innerHTML.replace(/,/g, ""))) < Number(parseFloat(y.innerHTML.replace(/,/g, "")))) {
            shouldSwitch= true;
            break;
          }
        } else if (x.innerHTML=="N/A" || y.innerHTML=="N/A") {
            if(x.innerHTML=="N/A" && y.innerHTML!="N/A") {
                shouldSwitch = true;
                break;
            }
        } else {
          if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
            shouldSwitch= true;
            break;
          }
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++; 
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
</html>
<?php
    
    }

}
