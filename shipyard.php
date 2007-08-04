<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: shipyard.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "makebars.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'shipyard');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_shipyard_title;
updatecookie($db);

echo "<h1>" . $title. "</h1>\n";

if ($portinfo['port_type'] != 'shipyard')
{
    echo $l_noport . "<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$res = $db->Execute("SELECT * FROM {$db->prefix}ship_types WHERE buyable = 'Y'");
while (!$res->EOF)
{
    $ships[] = $res->fields;
    $res->MoveNext();
}

$lastship = end($ships);

echo '<div style="text-align:center;">';
echo "<font style=\"font-size: 1.2em;\" color=white><strong>Welcome to this Federation shipyard. We currently offer these models for sale.</font></div><p>";
?>

  <table width=100% border=1 cellpadding=5 cellspacing=0>
    <tr bgcolor=<?php echo $color_line2 ?>><td width=10% align=center>
    <font style="font-size: 1.2em;"  color=white><strong>Class</strong></font>
    </td>
    <td width=* align=center>
    <font style="font-size: 1.2em;" color=white><strong>Class Properties</strong></font>
    </tr>

<?php

$first = 1;

foreach ($ships as $curship)
{
    if (!isset($_GET['stype']))
    {
        $_GET['stype'] = $curship['type_id'];
    }
    echo "<tr><td align=center>" .
         "<a style=\"text-decoration: none\" href=shipyard.php?stype=$curship[type_id]><img src='templates/$templateset/images/$curship[image]' /><br>" .
         "<font style=\"font-size: 0.8em;\" color=white><strong>$curship[name] </strong>Class</font></a>";

    if ($curship['type_id'] == $shipinfo['class'])
    {
        echo "<font color=white><br>(Current)</font>";
    }

    if ($first == 1)
    {
        $first = 0;
        echo "</td><td rowspan=100 valign=top>";

        if (isset($_GET['stype']))
        {
            // Get info for selected ship class
            foreach ($ships as $testship)
            {
                if ($testship['type_id'] == $_GET['stype'])
                {
                    $sship = $testship;
                    break;
                }
            }

            $hull_bars = MakeBars($sship['maxhull'], 100);
            $engines_bars = MakeBars($sship['maxengines'], 100);
            if ($plasma_engines)
            {
                $pengines_bars = MakeBars($sship['maxpengines'], 100);
            }

            $power_bars = MakeBars($sship['maxpower'], 100);
            $computer_bars = MakeBars($sship['maxcomputer'], 100);
            $sensors_bars = MakeBars($sship['maxsensors'], 100);
            $armor_bars = MakeBars($sship['maxarmor'], 100);
            $shields_bars = MakeBars($sship['maxshields'], 100);
            $beams_bars = MakeBars($sship['maxbeams'], 100);
            $torp_launchers_bars = MakeBars($sship['maxtorp_launchers'], 100);
            $cloak_bars = MakeBars($sship['maxcloak'], 100);

            $calc_nhull = round(pow($upgrade_factor,$sship['minhull']));
            $calc_nengines = round(pow($upgrade_factor,$sship['minengines']));

            if ($plasma_engines)
            {
                $calc_npengines = round(pow($upgrade_factor,$sship['minpengines']));
            }

            $calc_npower = round(pow($upgrade_factor,$sship['minpower']));
            $calc_ncomputer = round(pow($upgrade_factor,$sship['mincomputer']));
            $calc_nsensors = round(pow($upgrade_factor,$sship['minsensors']));
            $calc_nbeams = round(pow($upgrade_factor,$sship['minbeams']));
            $calc_ntorp_launchers = round(pow($upgrade_factor,$sship['mintorp_launchers']));
            $calc_nshields = round(pow($upgrade_factor,$sship['minshields']));
            $calc_narmor = round(pow($upgrade_factor,$sship['minarmor']));
            $calc_ncloak = round(pow($upgrade_factor,$sship['mincloak']));
            if ($plasma_engines)
            {
                $newshipvalue = ($calc_nhull+$calc_nengines+$calc_npengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmor+$calc_ncloak) * $upgrade_cost;
            }
            else
            {
                $newshipvalue = ($calc_nhull+$calc_nengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmor+$calc_ncloak) * $upgrade_cost;
            }

//            $float = (float) $newshipvalue;
//            echo number_format($float, 0, $local_number_dec_point, $local_number_thousands_sep);

            echo "<table border=0 cellpadding=0>" .
                 "<tr><td valign=top>" .
                 "<font color=white><strong>$sship[name]</strong></font><p>" .
                 "<font style=\"font-size: 0.8em;\" color=silver><strong>$sship[description]</strong></font><p>" .
                 "</td><td valign=top><img src='templates/$templateset/images/$sship[image]' /></td></tr>" .
                 "</table>" .
                 "<table border=0 cellpadding=0>" .
                 "<tr><td valign=top><font color=white><strong>Ship Components Levels</strong></font><br>&nbsp;</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minhull] / Max: $sship[maxhull])&nbsp;&nbsp;</font>" .
                 "$l_hull&nbsp;</strong></font>" .
                 "<td valign=bottom>$hull_bars</td></td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minengines] / Max: $sship[maxengines])&nbsp;&nbsp;</font>" .
                 "$l_engines&nbsp;</strong></font>" .
                 "<td valign=bottom>$engines_bars</td></td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>";
                 if ($plasma_engines)
                 {
                     echo "(Min: $sship[minpengines] / Max: $sship[maxpengines])&nbsp;&nbsp;</font>" .
                     "$l_pengines&nbsp;</strong></font>" .
                     "<td valign=bottom>$pengines_bars</td></td></tr>" .
                     "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>";
                 }

            echo "(Min: $sship[minpower] / Max: $sship[maxpower])&nbsp;&nbsp;</font>" .
                 "$l_power&nbsp;</strong></font>" .
                 "<td valign=bottom>$power_bars</td></td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[mincomputer] / Max: $sship[maxcomputer])&nbsp;&nbsp;</font>" .
                 "$l_computer&nbsp;</strong></font>" .
                 "<td valign=bottom>$computer_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minsensors] / Max: $sship[maxsensors])&nbsp;&nbsp;</font>" .
                 "$l_sensors&nbsp;</strong></font>" .
                 "<td valign=bottom>$sensors_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minarmor] / Max: $sship[maxarmor])&nbsp;&nbsp;</font>" .
                 "$l_armor&nbsp;</strong></font>" .
                 "<td valign=bottom>$armor_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minshields] / Max: $sship[maxshields])&nbsp;&nbsp;</font>" .
                 "$l_shields&nbsp;</strong></font>" .
                 "<td valign=bottom>$shields_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[minbeams] / Max: $sship[maxbeams])&nbsp;&nbsp;</font>" .
                 "$l_beams&nbsp;&nbsp;</strong></font>" .
                 "<td valign=bottom>$beams_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[mintorp_launchers] / Max: $sship[maxtorp_launchers])&nbsp;&nbsp;</font>" .
                 "$l_torp_launch&nbsp;</strong></font>" .
                 "<td valign=bottom>$torp_launchers_bars</td></tr>" .
                 "<tr><td><font style=\"font-size: 0.8em;\"><strong><font color=white>" .
                 "(Min: $sship[mincloak] / Max: $sship[maxcloak])&nbsp;&nbsp;</font>" .
                 "$l_cloak&nbsp;</strong></font>" .
                 "<td valign=bottom>$cloak_bars</td></tr>" .
                 "<tr><td><font color=white><strong><br>Price: </strong></td>" .
                 "<td><font color=red><strong><br>" . number_format($newshipvalue, 0, $local_number_dec_point, $local_number_thousands_sep) . " C</strong></td>" .
                 "</tr>" .
                 "<tr><td><font color=white><strong><br>Turns to build: </strong></td>" .
                 "<td><font color=red><strong><br>" . number_format($sship['turnstobuild'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</strong></td>" .
                 "</tr>" .
                 "</table><p>";

            if ($_GET['stype'] != $shipinfo['class'])
            {
                echo '<form name="bntform" action="shipyard2.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">' .
                     "<input type=hidden name=stype value=$_GET[stype]>" .
                     "&nbsp;<input name=submit_button type=submit value=Purchase>" .
                     "</form>";
            }

        }
        echo "</td></tr>";

    }
    else
    {
        echo "</td></tr>";
    }
}

$smarty->assign("sship_name", $sship['name']);
$smarty->assign("sship_description", $sship['description']);
$smarty->assign("sship_image", $sship['image']);
$smarty->assign("sship_minhull", $sship['minhull']);
$smarty->assign("sship_maxhull", $sship['maxhull']);
$smarty->assign("l_hull", $l_hull);
$smarty->assign("hull_bars", $hull_bars);
$smarty->assign("sship_minengines", $sship['minengines']);
$smarty->assign("sship_maxengines", $sship['maxengines']);
if ($plasma_engines)
{
    $smarty->assign("sship_minpengines", $sship['minpengines']);
    $smarty->assign("sship_maxpengines", $sship['maxpengines']);
    $smarty->assign("l_pengines", $l_pengines);
    $smarty->assign("pengines_bars", $pengines_bars);
}

$smarty->assign("l_engines", $l_engines);
$smarty->assign("engines_bars", $engines_bars);
$smarty->assign("sship_minpower", $sship['minpower']);
$smarty->assign("sship_maxpower", $sship['maxpower']);
$smarty->assign("l_power", $l_power);
$smarty->assign("power_bars", $power_bars);
$smarty->assign("sship_mincomputer", $sship['mincomputer']);
$smarty->assign("sship_maxcomputer", $sship['maxcomputer']);
$smarty->assign("l_computer", $l_computer);
$smarty->assign("computer_bars", $computer_bars);
$smarty->assign("sship_minsensors", $sship['minsensors']);
$smarty->assign("sship_maxsensors", $sship['maxsensors']);
$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("sensors_bars", $sensors_bars);
$smarty->assign("sship_minarmor", $sship['minarmor']);
$smarty->assign("sship_maxarmor", $sship['maxarmor']);
$smarty->assign("l_armor", $l_armor);
$smarty->assign("armor_bars", $armor_bars);
$smarty->assign("sship_minshields", $sship['minshields']);
$smarty->assign("sship_maxshields", $sship['maxshields']);
$smarty->assign("l_shields", $l_shields);
$smarty->assign("shields_bars", $shields_bars);
$smarty->assign("sship_minbeams", $sship['minbeams']);
$smarty->assign("sship_maxbeams", $sship['maxbeams']);
$smarty->assign("l_beams", $l_beams);
$smarty->assign("beams_bars", $beams_bars);
$smarty->assign("sship_mintorp_launchers", $sship['mintorp_launchers']);
$smarty->assign("sship_maxtorp_launchers", $sship['maxtorp_launchers']);
$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("torp_launchers_bars", $torp_launchers_bars);
$smarty->assign("sship_mincloak", $sship['mincloak']);
$smarty->assign("sship_maxcloak", $sship['maxcloak']);
$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("cloak_bars", $cloak_bars);
$smarty->assign("newshipvalue", $newshipvalue);
$smarty->assign("sship_turnstobuild", $sship['turnstobuild']);
$smarty->assign("shipinfo_class", $shipinfo['class']);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("stype",$_GET['stype']);
$smarty->display("$templateset/shipyard.tpl");

global $allow_ibank, $l_igb_term, $l_ifyouneedmore;

if ($allow_ibank)
{
    $igblink = "\n<a href=\"igb_login.php\">$l_igb_term</a>";
    $l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);
    echo "$l_ifyouneedmore<br>";
}   

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
