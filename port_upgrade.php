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
// File: port_upgrade.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "num_level.php");

$title = $l_upgrade_port_title;
echo "<h1>" . $title. "</h1>\n";
if (isLoanPending($playerinfo['player_id']))
{
    echo "$l_port_loannotrade<p>";
    echo "<a href=igb_login.php>$l_igb_term</a><p>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM {$db->prefix}bounty WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
if ($res2)
{
    $bty = $res2->fields;
    if ($bty['total_bounty'] > 0)
    {
        if ($pay != 1)
        {
            echo $l_port_bounty . "<br>";
            $l_port_bounty2 = str_replace("[amount]",number_format($bty['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_port_bounty2);
            echo "<a href=\"port.php?pay=1\">" . $l_port_bounty2 . "</a><br>";
            echo "<a href=\"bounty.php\">$l_by_placebounty</a><br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            die();
        }
        else
        {
            if ($playerinfo['credits'] < $bty['total_bounty'])
            {
                $l_port_btynotenough = str_replace("[amount]",number_format($bty['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_port_btynotenough);
                echo $l_port_btynotenough . "<br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                die();
            }
            else
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-$bty[total_bounty] WHERE player_id = $playerinfo[player_id]");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                $debug_query = $db->Execute("DELETE from {$db->prefix}bounty WHERE bounty_on = $playerinfo[player_id] AND placed_by = 0");
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo $l_port_bountypaid . "<br><a href=\"port.php\">" . $l_port_bountypaid2 . "</a><br>";
                die();
            }
        }
    }
}

$fighter_max = num_level($shipinfo['computer'], $level_factor, $level_magnitude);
$fighter_free = $fighter_max - $shipinfo['fighters'];
$torpedo_max = num_level($shipinfo['torp_launchers'], $level_factor, $level_magnitude);
$torpedo_free = $torpedo_max - $shipinfo['torps'];
$armor_max = num_level($shipinfo['armor'], $level_factor, $level_magnitude);
$armor_free = $armor_max - $shipinfo['armor_pts'];
$colonist_max = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'];
$colonist_free = $colonist_max - $shipinfo['colonists'];

echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";

echo "\n<script type=\"text/javascript\" defer=\"defer\">\n";
echo "<!--\n";

echo "function MakeMax(name, val)\n";
echo "{\n";
echo " if (document.forms[0].elements[name].value != val)\n";
echo " {\n";
echo "  if (val != 0)\n";
echo "  {\n";
echo "  document.forms[0].elements[name].value = val;\n";
echo "  }\n";
echo " }\n";
echo "}\n";

// changeDelta function //
echo "function changeDelta(desiredvalue,currentvalue)\n";
echo "{\n";
echo "  Delta=0; DeltaCost=0;\n";
echo "  Delta = desiredvalue - currentvalue;\n";
echo "\n";
echo "    while (Delta>0) \n";
echo "    {\n";
echo "     DeltaCost=DeltaCost + Math.pow($upgrade_factor,desiredvalue-Delta); \n";
echo "     Delta=Delta-1;\n";
echo "    }\n";
echo "\n";
echo "  DeltaCost=DeltaCost * $upgrade_cost\n";
echo "  return Math.round(DeltaCost*Math.pow(10,0))/Math.pow(10,0);\n";
echo "}\n";

echo "function counttotal()\n";
echo "{\n";
echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
echo "var form = document.forms[0];\n";
echo "var i = form.elements.length;\n";
echo "while (i > 0)\n";
echo "  {\n";
echo " if (form.elements[i-1].value == '')\n";
echo "  {\n";
echo "  form.elements[i-1].value ='0';\n";
echo "  }\n";
echo " i--;\n";
echo "}\n";
echo "// Here we set all 'Max' items to 0 if they are over max - player amt.\n";
echo "if (($fighter_free < form.fighter_number.value) && (form.fighter_number.value != 'Full'))\n";
echo " {\n";
echo " form.fighter_number.value=0\n";
echo " }\n";
echo "if (($torpedo_free < form.torpedo_number.value) && (form.torpedo_number.value != 'Full'))\n";
echo "  {\n";
echo "  form.torpedo_number.value=0\n";
echo "  }\n";
echo "if (($armor_free < form.armor_number.value) && (form.armor_number.value != 'Full'))\n";
echo "  {\n";
echo "  form.armor_number.value=0\n";
echo "  }\n";
echo "if (($colonist_free < form.colonist_number.value) && (form.colonist_number.value != 'Full' ))\n";
echo "  {\n";
echo "  form.colonist_number.value=0\n";
echo "  }\n";
echo "// Done with the bounds checking\n";
echo "// Pluses must be first, or if empty will produce a javascript error\n";
echo "form.total_cost.value =\n";
echo "changeDelta(form.hull_upgrade.value,$shipinfo[hull])\n";
echo "+ changeDelta(form.engine_upgrade.value,$shipinfo[engines])\n";
if ($plasma_engines)
{
    echo "+ changeDelta(form.pengine_upgrade.value,$shipinfo[pengines])\n";
}

echo "+ changeDelta(form.power_upgrade.value,$shipinfo[power])\n";
echo "+ changeDelta(form.computer_upgrade.value,$shipinfo[computer])\n";
echo "+ changeDelta(form.sensors_upgrade.value,$shipinfo[sensors])\n";
echo "+ changeDelta(form.beams_upgrade.value,$shipinfo[beams])\n";
echo "+ changeDelta(form.armor_upgrade.value,$shipinfo[armor])\n";
echo "+ changeDelta(form.cloak_upgrade.value,$shipinfo[cloak])\n";
echo "+ changeDelta(form.torp_launchers_upgrade.value,$shipinfo[torp_launchers])\n";
echo "+ changeDelta(form.shields_upgrade.value,$shipinfo[shields])\n";

if ($shipinfo['fighters'] != $fighter_max)
{
    echo "+ form.fighter_number.value * $fighter_price ";
}

if ($shipinfo['torps'] != $torpedo_max)
{
    echo "+ form.torpedo_number.value * $torpedo_price ";
}

if ($shipinfo['armor_pts'] != $armor_max)
{
    echo "+ form.armor_number.value * $armor_price ";
}

if ($shipinfo['colonists'] != $colonist_max)
{
    echo "+ form.colonist_number.value * $colonist_price ";
}

echo ";\n";

$i = $shipinfo['hull'];
while ($i <= $classinfo['maxhull'])
{
    $hulloptions[$i] = $i;
    $i++;
}

$i = $shipinfo['engines'];
while ($i <= $classinfo['maxengines'])
{
    $engineoptions[$i] = $i;
    $i++;
}

$i = $shipinfo['pengines'];
while ($i <= $classinfo['maxpengines'])
{
    $pengineoptions[$i] = $i;
    $i++;
}

$i = $shipinfo['power'];
while ($i <= $classinfo['maxpower'])
{
    $poweroptions[$i] = $i;
    $i++;
}

$i = $shipinfo['computer'];
while ($i <= $classinfo['maxcomputer'])
{
    $computeroptions[$i] = $i;
    $i++;
}

$i = $shipinfo['sensors'];
while ($i <= $classinfo['maxsensors'])
{
    $sensorsoptions[$i] = $i;
    $i++;
}

$i = $shipinfo['beams'];
while ($i <= $classinfo['maxbeams'])
{
    $beamsoptions[$i] = $i;
    $i++;
}

$i = $shipinfo['armor'];
while ($i <= $classinfo['maxarmor'])
{
    $armoroptions[$i] = $i;
    $i++;
}

$i = $shipinfo['cloak'];
while ($i <= $classinfo['maxcloak'])
{
    $cloakoptions[$i] = $i;
    $i++;
}

$i = $shipinfo['torp_launchers'];
while ($i <= $classinfo['maxtorp_launchers'])
{
    $tloptions[$i] = $i;
    $i++;
}

$i = $shipinfo['shields'];
while ($i <= $classinfo['maxshields'])
{
    $shieldoptions[$i] = $i;
    $i++;
}

$igblink = "\n<a href=igb_login.php>$l_igb_term</a>";
$l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);
$l_creds_to_spend=str_replace("[credits]",number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_creds_to_spend);

$smarty->assign("playerinfo_credits", $playerinfo['credits']);
$smarty->assign("l_no_credits", $l_no_credits);
$smarty->assign("l_creds_to_spend", $l_creds_to_spend);
$smarty->assign("allow_ibank", $allow_ibank);
$smarty->assign("l_ifyouneedmore", $l_ifyouneedmore);
$smarty->assign("l_by_placebounty", $l_by_placebounty);
$smarty->assign("l_ship_levels", $l_ship_levels);
$smarty->assign("l_cost", $l_cost);
$smarty->assign("l_current_level", $l_current_level);
$smarty->assign("l_upgrade", $l_upgrade);
$smarty->assign("l_hull", $l_hull);
$smarty->assign("number_shipinfo_hull", number_format($shipinfo['hull'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_hull", $shipinfo['hull']);
$smarty->assign("hullselected", $shipinfo['hull']);
$smarty->assign("hulloptions", $hulloptions);
$smarty->assign("l_engines", $l_engines);
$smarty->assign("number_shipinfo_engines", number_format($shipinfo['engines'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_engines", $shipinfo['engines']);
$smarty->assign("engineselected", $shipinfo['engines']);
$smarty->assign("engineoptions", $engineoptions);
$smarty->assign("plasma_engines", $plasma_engines);
$smarty->assign("l_pengines", $l_pengines);
$smarty->assign("number_shipinfo_pengines", number_format($shipinfo['pengines'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_pengines", $shipinfo['pengines']);
$smarty->assign("penginesselected", $shipinfo['pengines']);
$smarty->assign("pengineoptions", $pengineoptions);
$smarty->assign("l_power", $l_power);
$smarty->assign("number_shipinfo_power", number_format($shipinfo['power'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_power", $shipinfo['power']);
$smarty->assign("powerselected", $shipinfo['power']);
$smarty->assign("poweroptions", $poweroptions);
$smarty->assign("l_computer", $l_computer);
$smarty->assign("number_shipinfo_computer", number_format($shipinfo['computer'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_computer", $shipinfo['computer']);
$smarty->assign("computerselected", $shipinfo['computer']);
$smarty->assign("computeroptions", $computeroptions);
$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("number_shipinfo_sensors", number_format($shipinfo['sensors'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_sensors", $shipinfo['sensors']);
$smarty->assign("sensorsselected", $shipinfo['sensors']);
$smarty->assign("sensorsoptions", $sensorsoptions);
$smarty->assign("l_beams", $l_beams);
$smarty->assign("number_shipinfo_beams", number_format($shipinfo['beams'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_beams", $shipinfo['beams']);
$smarty->assign("beamsselected", $shipinfo['beams']);
$smarty->assign("beamsoptions", $beamsoptions);
$smarty->assign("l_armor", $l_armor);
$smarty->assign("number_shipinfo_armor", number_format($shipinfo['armor'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_armor", $shipinfo['armor']);
$smarty->assign("armorselected", $shipinfo['armor']);
$smarty->assign("armoroptions", $armoroptions);
$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("number_shipinfo_cloak", number_format($shipinfo['cloak'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_cloak", $shipinfo['cloak']);
$smarty->assign("cloakselected", $shipinfo['cloak']);
$smarty->assign("cloakoptions", $cloakoptions);
$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("number_shipinfo_torp_launchers", number_format($shipinfo['torp_launchers'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_torp_launchers", $shipinfo['torp_launchers']);
$smarty->assign("tlselected", $shipinfo['torp_launchers']);
$smarty->assign("tloptions", $tloptions);
$smarty->assign("l_shields", $l_shields);
$smarty->assign("number_shipinfo_shields", number_format($shipinfo['shields'], 0, $local_number_dec_point, $local_number_thousands_sep));
$smarty->assign("shipinfo_shields", $shipinfo['shields']);
$smarty->assign("shieldselected", $shipinfo['shields']);
$smarty->assign("shieldoptions", $shieldoptions);
$smarty->assign("l_item", $l_item);
$smarty->assign("l_cost", $l_cost);
$smarty->assign("l_current", $l_current);
$smarty->assign("l_max", $l_max);
$smarty->assign("l_qty", $l_qty);
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("number_fighter_price", number_format($fighter_price), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("number_shipinfo_fighters", number_format($shipinfo['fighters']), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("shipinfo_fighters", $shipinfo['fighters']);
$smarty->assign("number_fighter_max", number_format($fighter_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("fighter_max", $fighter_max);
$smarty->assign("number_fighter_free", number_format($fighter_free), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("fighter_free", $fighter_free);
$smarty->assign("l_torps", $l_torps);
$smarty->assign("number_torpedo_price", number_format($torpedo_price), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("number_shipinfo_torps", number_format($shipinfo['torps']), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("shipinfo_torps", $shipinfo['torps']);
$smarty->assign("number_torpedo_max", number_format($torpedo_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("torpedo_max", $torpedo_max);
$smarty->assign("torpedo_free", $torpedo_free);
$smarty->assign("number_torpedo_free", number_format($torpedo_free), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_armorpts", $l_armorpts);
$smarty->assign("number_armor_price", number_format($armor_price), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("armor_free", $armor_free);
$smarty->assign("number_armor_free", number_format($armor_free), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("number_shipinfo_armor_pts", number_format($shipinfo['armor_pts']), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("shipinfo_armor_pts", $shipinfo['armor_pts']);
$smarty->assign("number_armor_max", number_format($armor_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("armor_max", $armor_max);
$smarty->assign("l_colonists", $l_colonists);
$smarty->assign("number_colonist_price", number_format($colonist_price), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("colonist_free", $colonist_free);
$smarty->assign("number_colonist_free", number_format($colonist_free), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("shipinfo_colonists", $shipinfo['colonists']);
$smarty->assign("number_shipinfo_colonists", number_format($shipinfo['colonists']), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("colonist_max", $colonist_max);
$smarty->assign("number_colonist_max", number_format($colonist_max), 0, $local_number_dec_point, $local_number_thousands_sep);
$smarty->assign("l_credits_needed", $l_credits_needed);
$smarty->assign("l_full", $l_full);
$smarty->assign("l_buy", $l_buy);
$smarty->assign("l_totalcost", $l_totalcost);
$smarty->assign("l_would_dump", $l_would_dump);
$smarty->assign("templateset", $templateset);
$smarty->display("$templateset/port_upgrade.tpl");
?>