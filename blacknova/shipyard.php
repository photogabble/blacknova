<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_shipyard_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

bigtitle();

$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE buyable = 'Y'");
while(!$res->EOF)
{
  $ships[] = $res->fields;
  $res->MoveNext();
}

if(isset($stype))
{
  $lastship = end($ships);
  if($stype < 1 || $stype > $lastship[type_id])
    unset($stype);
}

echo "<center><font size=2 color=white><b>Welcome to this Federation shipyard. We currently offer these models for sale.</font></center><p>";
?>

  <table width=100% border=1 cellpadding=5>
    <tr bgcolor=<? echo $color_line2 ?>><td width=10% align=center>
    <font size=2 color=white><b>Class</b></font>
    </td>
    <td width=* align=center>
    <font size=2 color=white><b>Class Properties</b></font>
    </tr>
    <?
    $first=1;
    foreach($ships as $curship)
    {
      echo "<tr><td align=center>" .
           "<a style=\"text-decoration: none\" href=shipyard.php?stype=$curship[type_id]><img style=\"border: none\" src=images/$curship[image]><br>" .
           "<font size=2 color=white>Class <b>$curship[name]</a></b></font>";
      
      if($curship[type_id] == $shipinfo['class'])
        echo "<font size=2 color=white><br>(Current)</font>";

      if($first == 1)
      {
        $first = 0;
        echo "</td><td rowspan=100 valign=top>";
        
        if(!isset($stype))
          echo "<center><b>No ship class selected</b></center>";
        else
        {
          //get info for selected ship class
          foreach($ships as $testship)
          {
            if($testship[type_id] == $stype)
            {
              $sship = $testship;
              break;
            }
          }
          
          $hull_bars = MakeBars($sship[minhull], $sship[maxhull]);
          $engines_bars = MakeBars($sship[minengines], $sship[maxengines]);
          $power_bars = MakeBars($sship[minpower], $sship[maxpower]);
          $computer_bars = MakeBars($sship[mincomputer], $sship[maxcomputer]);
          $sensors_bars = MakeBars($sship[minsensors], $sship[maxsensors]);
          $armour_bars = MakeBars($sship[minarmour], $sship[maxarmour]);
          $shields_bars = MakeBars($sship[minshields], $sship[maxshields]);
          $beams_bars = MakeBars($sship[minbeams], $sship[maxbeams]);
          $torp_launchers_bars = MakeBars($sship[mintorp_launchers], $sship[maxtorp_launchers]);
          $cloak_bars = MakeBars($sship[mincloak], $sship[maxcloak]);
          
          echo "<table border=0 cellpadding=5>" .
               "<tr><td valign=top>" .
               "<font size=4 color=white><b>$sship[name]</b></font><p>" .
               "<font size=2 color=silver><b>$sship[description]</b></font><p>" .
               "</td><td valign=top><img src=images/$sship[image]></td></tr>" .
               "</table>" .
               "<table border=0 cellpadding=2>" .
               "<tr><td valign=top><font size=4 color=white><b>Ship Components Levels</b></font><br>&nbsp;</td></tr>" .
               "<tr><td><font size=2><b>Hull</b></td>" .
               "<td valign=bottom>$hull_bars</td></tr>" .
               "<tr><td><font size=2><b>Engines</b></td>" .
               "<td valign=bottom>$engines_bars</td></tr>" .
               "<tr><td><font size=2><b>Power</b></td>" .
               "<td valign=bottom>$power_bars</td></tr>" .
               "<tr><td><font size=2><b>Computer</b></td>" .
               "<td valign=bottom>$computer_bars</td></tr>" .
               "<tr><td><font size=2><b>Sensors</b></td>" .
               "<td valign=bottom>$sensors_bars</td></tr>" .
               "<tr><td><font size=2><b>Armour</b></td>" .
               "<td valign=bottom>$armour_bars</td></tr>" .
               "<tr><td><font size=2><b>Shields</b></td>" .
               "<td valign=bottom>$shields_bars</td></tr>" .
               "<tr><td><font size=2><b>Beams</b></td>" .
               "<td valign=bottom>$beams_bars</td></tr>" .
               "<tr><td><font size=2><b>Torpedo Launchers</b></td>" .
               "<td valign=bottom>$torp_launchers_bars</td></tr>" .
               "<tr><td><font size=2><b>Cloak</b></td>" .
               "<td valign=bottom>$cloak_bars</td></tr>" .
               "<tr><td><font color=white size=4><b><br>Price: </b></td>" .
               "<td><font color=red size=4><b><br>" . NUMBER($sship[cost_credits]) . " C</b></td></tr>" .
               "</table><p>";
          
          if($stype != $shipinfo['class'])
          {
            echo "<form action=shipyard2.php method=POST>" .
                 "<input type=hidden name=stype value=$stype>" .
                 "&nbsp;<input type=submit value=Purchase>" .
                 "</form>";
          }
              
        }
        
        echo "</td></tr>";
      }     
      else
        echo "</td></tr>";
    }
    ?>
  
  </table>
  <p>
<?

TEXT_GOTOMAIN();

include("footer.php");

?>