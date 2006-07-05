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
// File: port2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "buy_them.php"); 
dynamic_loader ($db, "num_level.php"); 
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'spy');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_title_port;
updatecookie($db);
include_once ("./header.php");

if (!isset($_POST['trade_ore']))
{
    $_POST['trade_ore'] = '';
}
else
{
    $_POST['trade_ore'] = preg_replace('/[^0-9]/','',$_POST['trade_ore']);
}

if (!isset($_POST['trade_organics']))
{
    $_POST['trade_organics'] = '';
}
else
{
    $_POST['trade_organics'] = preg_replace('/[^0-9]/','',$_POST['trade_organics']);
}

if (!isset($_POST['trade_goods']))
{
    $_POST['trade_goods'] = '';
}
else
{
    $_POST['trade_goods'] = preg_replace('/[^0-9]/','',$_POST['trade_goods']);
}

if (!isset($_POST['trade_energy']))
{
    $_POST['trade_energy'] = '';
}
else
{
    $_POST['trade_energy'] = preg_replace('/[^0-9]/','',$_POST['trade_energy']);
}

if (!isset($_POST['hull_upgrade']))
{
    $_POST['hull_upgrade'] = '';
}
else
{
    $_POST['hull_upgrade'] = preg_replace('/[^0-9]/','',$_POST['hull_upgrade']);
}

if (!isset($_POST['engine_upgrade']))
{
    $_POST['engine_upgrade'] = '';
}
else
{
    $_POST['engine_upgrade'] = preg_replace('/[^0-9]/','',$_POST['engine_upgrade']);
}

if (!isset($_POST['pengine_upgrade']))
{
    $_POST['pengine_upgrade'] = '';
}
else
{
    $_POST['pengine_upgrade'] = preg_replace('/[^0-9]/','',$_POST['pengine_upgrade']);
}

if (!isset($_POST['power_upgrade']))
{
    $_POST['power_upgrade'] = '';
}
else
{
    $_POST['power_upgrade'] = preg_replace('/[^0-9]/','',$_POST['power_upgrade']);
}

if (!isset($_POST['computer_upgrade']))
{
    $_POST['computer_upgrade'] = '';
}
else
{
    $_POST['computer_upgrade'] = preg_replace('/[^0-9]/','',$_POST['computer_upgrade']);
}

if (!isset($_POST['sensors_upgrade']))
{
    $_POST['sensors_upgrade'] = '';
}
else
{
    $_POST['sensors_upgrade'] = preg_replace('/[^0-9]/','',$_POST['sensors_upgrade']);
}

if (!isset($_POST['beams_upgrade']))
{
    $_POST['beams_upgrade'] = '';
}
else
{
    $_POST['beams_upgrade'] = preg_replace('/[^0-9]/','',$_POST['beams_upgrade']);
}

if (!isset($_POST['armor_upgrade']))
{
    $_POST['armor_upgrade'] = '';
}
else
{
    $_POST['armor_upgrade'] = preg_replace('/[^0-9]/','',$_POST['armor_upgrade']);
}

if (!isset($_POST['cloak_upgrade']))
{
    $_POST['cloak_upgrade'] = '';
}
else
{
    $_POST['cloak_upgrade'] = preg_replace('/[^0-9]/','',$_POST['cloak_upgrade']);
}

if (!isset($_POST['torp_launchers_upgrade']))
{
    $_POST['torp_launchers_upgrade'] = '';
}
else
{
    $_POST['torp_launchers_upgrade'] = preg_replace('/[^0-9]/','',$_POST['torp_launchers_upgrade']);
}

if (!isset($_POST['shields_upgrade']))
{
    $_POST['shields_upgrade'] = '';
}
else
{
    $_POST['shields_upgrade'] = preg_replace('/[^0-9]/','',$_POST['shields_upgrade']);
}

if (!isset($_POST['fighter_number']))
{
    $_POST['fighter_number'] = '';
}
else
{
    $_POST['fighter_number'] = preg_replace('/[^0-9]/','',$_POST['fighter_number']);
}

if (!isset($_POST['torpedo_number']))
{
    $_POST['torpedo_number'] = '';
}
else
{
    $_POST['torpedo_number'] = preg_replace('/[^0-9]/','',$_POST['torpedo_number']);
}

if (!isset($_POST['armor_number']))
{
    $_POST['armor_number'] = '';
}
else
{
    $_POST['armor_number'] = preg_replace('/[^0-9]/','',$_POST['armor_number']);
}

if (!isset($_POST['colonist_number']))
{
    $_POST['colonist_number'] = '';
}
else
{
    $_POST['colonist_number'] = preg_replace('/[^0-9]/','',$_POST['colonist_number']);
}

if (!isset($_POST['escapepod_purchase']))
{
    $_POST['escapepod_purchase'] = '';
}
else
{
    $_POST['escapepod_purchase'] = preg_replace('/[^0-9]/','',$_POST['escapepod_purchase']);
}

if (!isset($_POST['fuelscoop_purchase']))
{
    $_POST['fuelscoop_purchase'] = '';
}
else
{
    $_POST['fuelscoop_purchase'] = preg_replace('/[^0-9]/','',$_POST['fuelscoop_purchase']);
}

if (!isset($sensors_upgrade_cost))
{
    $sensors_upgrade_cost = '';
}
else
{
    $sensors_upgrade_cost = preg_replace('/[^0-9]/','',$sensors_upgrade_cost);
}

if (!isset($_POST['dev_genesis_number']))
{
    $_POST['dev_genesis_number'] = '';
}
else
{
    $_POST['dev_genesis_number'] = preg_replace('/[^0-9]/','',$_POST['dev_genesis_number']);
}

if (!isset($_POST['dev_emerwarp_number']))
{
    $_POST['dev_emerwarp_number'] = '';
}
else
{
    $_POST['dev_emerwarp_number'] = preg_replace('/[^0-9]/','',$_POST['dev_emerwarp_number']);
}

if (!isset($_POST['dev_warpedit_number']))
{
    $_POST['dev_warpedit_number'] = '';
}
else
{
    $_POST['dev_warpedit_number'] = preg_replace('/[^0-9]/','',$_POST['dev_warpedit_number']);
}

if (!isset($_POST['dev_minedeflector_number']))
{
    $_POST['dev_minedeflector_number'] = '';
}
else
{
    $_POST['dev_minedeflector_number'] = preg_replace('/[^0-9]/','',$_POST['dev_minedeflector_number']);
}

if (!isset($_POST['spy_number']))
{
    $_POST['spy_number'] = '';
}
else
{
    $_POST['spy_number'] = preg_replace('/[^0-9]/','',$_POST['spy_number']);
}

//-------------------------------------------------------------------------------------------------

if ($zoneinfo['allow_trade'] == 'N')
{
    $title = $l_no_trade;
    echo "<h1>" . $title. "</h1>\n";
    echo "$l_no_trade_info<p>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
elseif ($zoneinfo['allow_trade'] == 'L')
{
    if ($zoneinfo[team_zone] == 'N')
    {
        $res = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($zoneinfo['owner']));
        $ownerinfo = $res->fields;

        if ($playerinfo[player_id] != $zoneinfo[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
        {
            $title = $l_no_trade;
            echo "<h1>" . $title. "</h1>\n";
            echo "$l_no_trade_out<p>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }
    }
    else
    {
        if ($playerinfo[team] != $zoneinfo[owner])
        {
            $title = $l_no_trade;
            echo "<h1>" . $title. "</h1>\n";
            echo "$l_no_trade_out<p>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }
    }
}

echo "<h1>" . $title. "</h1>\n";

$color_red     = "red";
$color_green   = "#00FF00"; // Light green
$trade_deficit = "$l_cost : ";
$trade_benefit = "$l_profit : ";

// Dynamic functions
dynamic_loader ($db, "buildonecol.php");
dynamic_loader ($db, "buildtwocol.php");
dynamic_loader ($db, "phpchangedelta.php");

if ($playerinfo['turns'] < 1)
{
    echo "$l_trade_turnneed<br><br>";
}
else
{
    $_POST['trade_ore']      = ROUND(abs($_POST['trade_ore']));
    $_POST['trade_organics'] = ROUND(abs($_POST['trade_organics']));
    $_POST['trade_goods']    = ROUND(abs($_POST['trade_goods']));
    $_POST['trade_energy']   = ROUND(abs($_POST['trade_energy']));

    if ($portinfo['port_type'] == "upgrades" || $portinfo['port_type'] == "devices")
    {
        if (isLoanPending($playerinfo['player_id']))
        {
            echo "$l_port_loannotrade<p>";
            echo "<a href=\"igb_login.php\">$l_igb_term</a><p>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        $hull_upgrade_cost = 0;
        if ($_POST['hull_upgrade'] > $classinfo['maxhull'])
        {
            $_POST['hull_upgrade'] = $classinfo['maxhull'];
        }

        if ($_POST['hull_upgrade'] < $classinfo['minhull'])
        {
            $_POST['hull_upgrade'] = $classinfo['minhull'];
        }

        if ($_POST['hull_upgrade'] > $shipinfo['hull'])
        {
            $hull_upgrade_cost = phpChangeDelta($_POST['hull_upgrade'], $shipinfo['hull'], $upgrade_cost, $upgrade_factor);
        }

        $engine_upgrade_cost = 0;
        if ($_POST['engine_upgrade'] > $classinfo['maxengines'])
        {
            $_POST['engine_upgrade'] = $classinfo['maxengines'];
        }

        if ($_POST['engine_upgrade'] < $classinfo['minengines'])
        {
            $_POST['engine_upgrade'] = $classinfo['minengines'];
        }

        if ($_POST['engine_upgrade'] > $shipinfo['engines'])
        {
            $engine_upgrade_cost = phpChangeDelta($_POST['engine_upgrade'], $shipinfo['engines'], $upgrade_cost, $upgrade_factor);
        }

        $pengine_upgrade_cost = 0;
        if ($_POST['pengine_upgrade'] > $classinfo['maxpengines'])
        {
            $_POST['pengine_upgrade'] = $classinfo['maxpengines'];
        }

        if ($_POST['pengine_upgrade'] < $classinfo['minpengines'])
        {
            $_POST['pengine_upgrade'] = $classinfo['minpengines'];
        }

        if ($_POST['pengine_upgrade'] > $shipinfo['pengines'])
        {
            $pengine_upgrade_cost = phpChangeDelta($_POST['pengine_upgrade'], $shipinfo['pengines'], $upgrade_cost, $upgrade_factor);
        }

        $power_upgrade_cost = 0;
        if ($_POST['power_upgrade'] > $classinfo['maxpower'])
        {
            $_POST['power_upgrade'] = $classinfo['maxpower'];
        }

        if ($_POST['power_upgrade'] < $classinfo['minpower'])
        {
            $_POST['power_upgrade'] = $classinfo['minpower'];
        }

        if ($_POST['power_upgrade'] > $shipinfo['power'])
        {
            $power_upgrade_cost = phpChangeDelta($_POST['power_upgrade'], $shipinfo['power'], $upgrade_cost, $upgrade_factor);
        }

        $computer_upgrade_cost = 0;
        if ($_POST['computer_upgrade'] > $classinfo['maxcomputer'])
        {
            $_POST['computer_upgrade'] = $classinfo['maxcomputer'];
        }

        if ($_POST['computer_upgrade'] < $classinfo['mincomputer'])
        {
            $_POST['computer_upgrade'] = $classinfo['mincomputer'];
        }

        if ($_POST['computer_upgrade'] > $shipinfo['computer'])
        {
            $computer_upgrade_cost = phpChangeDelta($_POST['computer_upgrade'], $shipinfo['computer'], $upgrade_cost, $upgrade_factor);
        }

        $sensor_upgrade_cost = 0;
        if ($_POST['sensors_upgrade'] > $classinfo['maxsensors'])
        {
            $_POST['sensors_upgrade'] = $classinfo['maxsensors'];
        }

        if ($_POST['sensors_upgrade'] < $classinfo['minsensors'])
        {
            $_POST['sensors_upgrade'] = $classinfo['minsensors'];
        }

        if ($_POST['sensors_upgrade'] > $shipinfo['sensors'])
        {
            $sensors_upgrade_cost = phpChangeDelta($_POST['sensors_upgrade'], $shipinfo['sensors'], $upgrade_cost, $upgrade_factor);
        }

        $beams_upgrade_cost = 0;
        if ($_POST['beams_upgrade'] > $classinfo['maxbeams'])
        {
            $_POST['beams_upgrade'] = $classinfo['maxbeams'];
        }

        if ($_POST['beams_upgrade'] < $classinfo['minbeams'])
        {
            $_POST['beams_upgrade'] = $classinfo['minbeams'];
        }

        if ($_POST['beams_upgrade'] > $shipinfo['beams'])
        {
            $beams_upgrade_cost = phpChangeDelta($_POST['beams_upgrade'], $shipinfo['beams'], $upgrade_cost, $upgrade_factor);
        }

        $armor_upgrade_cost = 0;
        if ($_POST['armor_upgrade'] > $classinfo['maxarmor'])
        {
            $_POST['armor_upgrade'] = $classinfo['maxarmor'];
        }

        if ($_POST['armor_upgrade'] < $classinfo['minarmor'])
        {
            $_POST['armor_upgrade'] = $classinfo['minarmor'];
        }

        if ($_POST['armor_upgrade'] > $shipinfo['armor'])
        {
            $armor_upgrade_cost = phpChangeDelta($_POST['armor_upgrade'], $shipinfo['armor'], $upgrade_cost, $upgrade_factor);
        }

        $cloak_upgrade_cost = 0;
        if ($_POST['cloak_upgrade'] > $classinfo['maxcloak'])
        {
            $_POST['cloak_upgrade'] = $classinfo['maxcloak'];
        }

        if ($_POST['cloak_upgrade'] < $classinfo['mincloak'])
        {
            $_POST['cloak_upgrade'] = $classinfo['mincloak'];
        }

        if ($_POST['cloak_upgrade'] > $shipinfo['cloak'])
        {
            $cloak_upgrade_cost = phpChangeDelta($_POST['cloak_upgrade'], $shipinfo['cloak'], $upgrade_cost, $upgrade_factor);
        }

        $torp_launchers_upgrade_cost = 0;
        if ($_POST['torp_launchers_upgrade'] > $classinfo['maxtorp_launchers'])
        {
            $_POST['torp_launchers_upgrade'] = $classinfo['maxtorp_launchers'];
        }

        if ($_POST['torp_launchers_upgrade'] < $classinfo['mintorp_launchers'])
        {
            $_POST['torp_launchers_upgrade'] = $classinfo['mintorp_launchers'];
        }

        if ($_POST['torp_launchers_upgrade'] > $shipinfo['torp_launchers'])
        {
            $torp_launchers_upgrade_cost = phpChangeDelta($_POST['torp_launchers_upgrade'], $shipinfo['torp_launchers'], $upgrade_cost, $upgrade_factor);
        }

        $shields_upgrade_cost = 0;
        if ($_POST['shields_upgrade'] > $classinfo['maxshields'])
        {
            $_POST['shields_upgrade'] = $classinfo['maxshields'];
        }

        if ($_POST['shields_upgrade'] < $classinfo['minshields'])
        {
            $_POST['shields_upgrade'] = $classinfo['minshields'];
        }

        if ($_POST['shields_upgrade'] > $shipinfo['shields'])
        {
            $shields_upgrade_cost = phpChangeDelta($_POST['shields_upgrade'], $shipinfo['shields'], $upgrade_cost, $upgrade_factor);
        }


        if ($_POST['fighter_number'] < 0)
        {
            $_POST['fighter_number'] = 0;
//          $_POST['fighter_number']  = round(abs($_POST['fighter_number']));
        }

        $fighter_max     = num_level($shipinfo['computer'], $level_factor, $level_magnitude) - $shipinfo['fighters'];
        if ($fighter_max < 0)
        {
            $fighter_max = 0;
        }

        if ($_POST['fighter_number'] > $fighter_max)
        {
            $_POST['fighter_number'] = $fighter_max;
        }

        $fighter_cost    = $_POST['fighter_number'] * $fighter_price;
        if ($_POST['torpedo_number'] < 0)
        {
            $_POST['torpedo_number'] = 0;
//          $_POST['torpedo_number']  = round(abs($_POST['torpedo_number']));
        }

        $torpedo_max     = num_level($shipinfo['torp_launchers'], $level_factor, $level_magnitude) - $shipinfo['torps'];
        if ($torpedo_max < 0)
        {
            $torpedo_max = 0;
        }

        if ($_POST['torpedo_number'] > $torpedo_max)
        {
            $_POST['torpedo_number'] = $torpedo_max;
        }

        $torpedo_cost = $_POST['torpedo_number'] * $torpedo_price;
        if ($_POST['armor_number'] < 0)
        {
            $_POST['armor_number'] = 0;
//          $_POST['armor_number'] = round(abs($_POST['armor_number']));
        }

        $armor_max = num_level($shipinfo['armor'],$level_factor, $level_magnitude) - $shipinfo['armor_pts'];
        if ($armor_max < 0)
        {
            $armor_max = 0;
        }

        if ($_POST['armor_number'] > $armor_max)
        {
            $_POST['armor_number'] = $armor_max;
        }

        $armor_cost     = $_POST['armor_number'] * $armor_price;
        if ($_POST['colonist_number'] < 0)
        {
            $_POST['colonist_number'] = 0;
//          $_POST['colonist_number'] = round(abs($_POST['colonist_number']));
        }

        $colonist_max    = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];

        if ($_POST['colonist_number'] > $colonist_max)
        {
            $_POST['colonist_number'] = $colonist_max;
        }

        $colonist_cost            = $_POST['colonist_number'] * $colonist_price;

//      $_POST['dev_genesis_number']       = round(abs($_POST['dev_genesis_number']));
        $dev_genesis_cost         = $_POST['dev_genesis_number'] * $dev_genesis_price;

        $_POST['dev_emerwarp_number']      = min($_POST['dev_emerwarp_number'], $max_emerwarp - $shipinfo['dev_emerwarp']);
        $dev_emerwarp_cost        = $_POST['dev_emerwarp_number'] * $dev_emerwarp_price;

//      $_POST['dev_warpedit_number']      = $_POST['dev_warpedit_number'];
        $dev_warpedit_cost        = $_POST['dev_warpedit_number'] * $dev_warpedit_price;

//      $_POST['dev_minedeflector_number'] = round(abs($_POST['dev_minedeflector_number']));
        $dev_minedeflector_cost   = $_POST['dev_minedeflector_number'] * $dev_minedeflector_price;

        if ($spy_success_factor)
        {
//          $_POST['spy_number']               = round(abs($_POST['spy_number']));
            $spy_cost                 = $_POST['spy_number'] * $spy_price;
        }
        else
        {
            $_POST['spy_number']               = 0;
            $spy_cost                 = 0;
        }

        $dev_escapepod_cost = 0;
        $dev_fuelscoop_cost = 0;

        if (($_POST['escapepod_purchase']) && ($shipinfo['dev_escapepod'] != 'Y'))
//      if ($_POST['escapepod_purchase'])
        {
            $dev_escapepod_cost = $dev_escapepod_price;
        }

        if (($_POST['fuelscoop_purchase']) && ($shipinfo['dev_fuelscoop'] != 'Y'))
        {
            $dev_fuelscoop_cost = $dev_fuelscoop_price;
        }

        $total_cost = $hull_upgrade_cost + $engine_upgrade_cost + $pengine_upgrade_cost + $power_upgrade_cost + 
        $computer_upgrade_cost + $sensors_upgrade_cost + $beams_upgrade_cost + $armor_upgrade_cost + $cloak_upgrade_cost +
        $torp_launchers_upgrade_cost + $fighter_cost + $torpedo_cost + $armor_cost + $colonist_cost +
        $dev_genesis_cost + $dev_emerwarp_cost + $dev_warpedit_cost + $dev_minedeflector_cost +
        $dev_escapepod_cost + $dev_fuelscoop_cost + $shields_upgrade_cost + $spy_cost;

        if ($total_cost > $playerinfo['credits'])
        {
            echo "You do not have enough credits for this transaction.  The total cost is " . number_format($total_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . 
                 " credits and you only have " . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " credits.";
        }
        else
        {
            $trade_credits = number_format($total_cost, 0, $local_number_dec_point, $local_number_thousands_sep);
            echo "<table border=2 cellspacing=2 cellpadding=2 bgcolor=\"" . $color_line2 . "\" width=600 align=center>
                    <tr>
                      <td colspan=99 align=center bgcolor=\"" . $color_line1 . "\"><font color=white><strong>$l_trade_result</strong></font></td>
                    </tr>
                    <tr>
                      <td colspan=99 align=center><strong><font color=red>$l_cost : " . $trade_credits . " $l_credits</font></strong></td>
                    </tr>";

            // Total cost is " . number_format($total_cost, 0, $local_number_dec_point, $local_number_thousands_sep) . " credits.<br><br>";
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-?,turns=turns-1, turns_used=turns_used+1 WHERE player_id=?", array($total_cost, $playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $query = "UPDATE {$db->prefix}ships SET class=? ";

            if ($_POST['hull_upgrade'] > $shipinfo['hull'])
            {
                $tempvar = 0; 
                $tempvar = $_POST['hull_upgrade'] - $shipinfo['hull'];
                $query = $query . ", hull=hull+". $db->qstr($tempvar);
                BuildOneCol("$l_hull $l_trade_upgraded $_POST[hull_upgrade]");
            }

            if ($_POST['engine_upgrade'] > $shipinfo['engines'])
            {
                $tempvar = 0;
                $tempvar = $_POST['engine_upgrade'] - $shipinfo['engines'];
                $query = $query . ", engines=engines+". $db->qstr($tempvar);
                BuildOneCol("$l_engines $l_trade_upgraded $_POST[engine_upgrade]");
            }

            if ($_POST['pengine_upgrade'] > $shipinfo['pengines'])
            {
                $tempvar = 0;
                $tempvar = $_POST['pengine_upgrade'] - $shipinfo['pengines'];
                $query = $query . ", pengines=pengines+". $db->qstr($tempvar);
                BuildOneCol("$l_pengines $l_trade_upgraded $_POST[pengine_upgrade]");
            }

            if ($_POST['power_upgrade'] > $shipinfo['power'])
            {
                $tempvar = 0;
                $tempvar = $_POST['power_upgrade'] - $shipinfo['power'];
                $query = $query . ", power=power+". $db->qstr($tempvar);
                BuildOneCol("$l_power $l_trade_upgraded $_POST[power_upgrade]");
            }

            if ($_POST['computer_upgrade'] > $shipinfo['computer'])
            {
                $tempvar = 0;
                $tempvar = $_POST['computer_upgrade'] - $shipinfo['computer'];
                $query = $query . ", computer=computer+". $db->qstr($tempvar);
                BuildOneCol("$l_computer $l_trade_upgraded $_POST[computer_upgrade]");
            }

            if ($_POST['sensors_upgrade'] > $shipinfo['sensors'])
            {
                $tempvar = 0; 
                $tempvar = $_POST['sensors_upgrade'] - $shipinfo['sensors'];
                $query = $query . ", sensors=sensors+". $db->qstr($tempvar);
                BuildOneCol("$l_sensors $l_trade_upgraded $_POST[sensors_upgrade]");
            }

            if ($_POST['beams_upgrade'] > $shipinfo['beams'])
            {
                $tempvar = 0; 
                $tempvar = $_POST['beams_upgrade'] - $shipinfo['beams'];
                $query = $query . ", beams=beams+". $db->qstr($tempvar);
                BuildOneCol("$l_beams $l_trade_upgraded $_POST[beams_upgrade]");
            }

            if ($_POST['armor_upgrade'] > $shipinfo['armor'])
            {
                $tempvar = 0;
                $tempvar = $_POST['armor_upgrade'] - $shipinfo['armor'];
                $query = $query . ", armor=armor+". $db->qstr($tempvar);
                BuildOneCol("$l_armor $l_trade_upgraded $_POST[armor_upgrade]");
            }

            if ($_POST['cloak_upgrade'] > $shipinfo['cloak'])
            {
                $tempvar = 0;
                $tempvar = $_POST['cloak_upgrade'] - $shipinfo['cloak'];
                $query = $query . ", cloak=cloak+". $db->qstr($tempvar);
                BuildOneCol("$l_cloak $l_trade_upgraded $_POST[cloak_upgrade]");
            }

            if ($_POST['torp_launchers_upgrade'] > $shipinfo['torp_launchers'])
            {
                $tempvar = 0;
                $tempvar = $_POST['torp_launchers_upgrade'] - $shipinfo['torp_launchers'];
                $query = $query . ", torp_launchers=torp_launchers+". $db->qstr($tempvar);
                BuildOneCol("$l_torp_launch $l_trade_upgraded $_POST[torp_launchers_upgrade]");
            }

            if ($_POST['shields_upgrade'] > $shipinfo['shields'])
            {
                $tempvar = 0;
                $tempvar = $_POST['shields_upgrade'] - $shipinfo['shields'];
                $query = $query . ", shields=shields+". $db->qstr($tempvar);
                BuildOneCol("$l_shields $l_trade_upgraded $_POST[shields_upgrade]");
            }

            if ($_POST['fighter_number'])
            {
                $query = $query . ", fighters=fighters+" . $db->qstr($_POST['fighter_number']);
                BuildTwoCol("$l_fighters $l_trade_added:", $_POST['fighter_number'], "left", "right" );
            }

            if ($_POST['torpedo_number'])
            {
                $query = $query . ", torps=torps+" . $db->qstr($_POST['torpedo_number']);
                BuildTwoCol("$l_torps $l_trade_added:", $_POST['torpedo_number'], "left", "right" );
            }

            if ($_POST['armor_number'])
            {
                $query = $query . ", armor_pts=armor_pts+". $db->qstr($_POST['armor_number']);
                BuildTwoCol("$l_armorpts $l_trade_added:", $_POST['armor_number'], "left", "right" );
            }

            if ($_POST['colonist_number'])
            {
                $query = $query . ", colonists=colonists+". $db->qstr($_POST['colonist_number']);
                BuildTwoCol("$l_colonists $l_trade_added:", $_POST['colonist_number'], "left", "right" );
            }

            if ($_POST['dev_genesis_number'])
            {
                $query = $query . ", dev_genesis=dev_genesis+". $db->qstr($_POST['dev_genesis_number']);
                BuildTwoCol("$l_genesis $l_trade_added:", $_POST['dev_genesis_number'], "left", "right" );
            }

            if ($_POST['dev_emerwarp_number'])
            {
                $query = $query . ", dev_emerwarp=dev_emerwarp+". $db->qstr($_POST['dev_emerwarp_number']);
                BuildTwoCol("$l_ewd $l_trade_added:", $_POST['dev_emerwarp_number'] , "left", "right" );
            }

            if ($_POST['dev_warpedit_number'])
            {
                $query = $query . ", dev_warpedit=dev_warpedit+". $db->qstr($_POST['dev_warpedit_number']);
                BuildTwoCol("$l_warpedit $l_trade_added:", $_POST['dev_warpedit_number'] , "left", "right" );
            }

            if ($_POST['dev_minedeflector_number'])
            {
                $query = $query . ", dev_minedeflector=dev_minedeflector+". $db->qstr($_POST['dev_minedeflector_number']);
                BuildTwoCol("$l_deflect $l_trade_added:", $_POST['dev_minedeflector_number'] , "left", "right" );
            }

            if (($_POST['escapepod_purchase']) && ($shipinfo['dev_escapepod'] != 'Y'))
            {
                $query = $query . ", dev_escapepod='Y'";
                BuildOneCol("$l_escape_pod $l_trade_installed");
            }

            if (($_POST['fuelscoop_purchase']) && ($shipinfo['dev_fuelscoop'] != 'Y'))
            {
                $query = $query . ", dev_fuelscoop='Y'";
                BuildOneCol("$l_fuel_scoop $l_trade_installed");
            }

            if ($_POST['spy_number'] && $spy_success_factor)
            {
                buy_them($db,$playerinfo['player_id'], $_POST['spy_number']);
                BuildTwoCol("$l_spy $l_trade_added:", $_POST['spy_number'] , "left", "right" );
            }
      
            $query = $query . " WHERE ship_id=?";
            $debug_query = $db->Execute($query, $shipinfo['class'], $shipinfo['ship_id']);
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $_POST['hull_upgrade']=0;
            echo "
              </table>
            ";
        }
    }
    elseif ($portinfo['port_type'] != "none")
    {
        //  Here is the port_trade fonction to strip out some "spaghetti code".
        //  The function saves about 60 lines of code, I hope it will be
        //  easier to modify/add something in this part. --Fant0m

        $price_array = array();

        // Dynamic functions
        dynamic_loader ($db, "port_trade.php");

        $_POST['trade_ore']       =  port_trade($ore_price,        $ore_delta,       $portinfo['port_ore'],        $ore_limit,       $inventory_factor, "ore",        $_POST['trade_ore']);
        $_POST['trade_organics']  =  port_trade($organics_price,   $organics_delta,  $portinfo['port_organics'],   $organics_limit,  $inventory_factor, "organics",   $_POST['trade_organics'] );
        $_POST['trade_goods']     =  port_trade($goods_price,      $goods_delta,     $portinfo['port_goods'],      $goods_limit,     $inventory_factor, "goods",      $_POST['trade_goods']);
        $_POST['trade_energy']    =  port_trade($energy_price,     $energy_delta,    $portinfo['port_energy'],     $energy_limit,    $inventory_factor, "energy",     $_POST['trade_energy']);

        $ore_price       =  $price_array['ore'];
        $organics_price  =  $price_array['organics'];
        $goods_price     =  $price_array['goods'];
        $energy_price    =  $price_array['energy'];

        $cargo_exchanged = $_POST['trade_ore'] + $_POST['trade_organics'] + $_POST['trade_goods'];

        $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - 
                      $shipinfo['colonists'];

        $free_power = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude)) - $shipinfo['energy'];

        $total_cost = '';
        $total_cost = $_POST['trade_ore'] * $ore_price + $_POST['trade_organics'] * $organics_price + $_POST['trade_goods'] * 
                      $goods_price + $_POST['trade_energy'] * $energy_price;

        $l_returnto_port = str_replace("[port_link]", "<a href=port.php>".$l_returnto_port1."</a>", $l_returnto_port2);

        if ($free_holds < $cargo_exchanged)
        {
            echo "$l_notenough_cargo  \n<br>$l_returnto_port<br><br>";
        }
        elseif ($_POST['trade_energy'] > $free_power)
        {
            echo "$l_notenough_power  \n<br>$l_returnto_port<br><br>";
        }
        elseif ($playerinfo['turns'] < 1)
        {
            echo "$l_notenough_turns.<br><br>";
        }
        elseif ($playerinfo['credits'] < $total_cost)
        {
            echo "$l_notenough_credits <br><br>";
        }
        elseif ($_POST['trade_ore'] < 0 && abs($shipinfo['ore']) < abs($_POST['trade_ore']))
        {
            echo "$l_notenough_ore ";
        }
        elseif ($_POST['trade_organics'] < 0 && abs($shipinfo['organics']) < abs($_POST['trade_organics']))
        {
            echo "$l_notenough_organics ";
        }
        elseif ($_POST['trade_goods'] < 0 && abs($shipinfo['goods']) < abs($_POST['trade_goods']))
        {
            echo "$l_notenough_goods ";
        }
        elseif ($_POST['trade_energy'] < 0 && abs($shipinfo['energy']) < abs($_POST['trade_energy']))
        {
            echo "$l_notenough_energy ";
        }
        elseif (abs($_POST['trade_organics']) > $portinfo['port_organics'])
        {
            echo $l_exceed_organics;
        }
        elseif (abs($_POST['trade_ore']) > $portinfo['port_ore'])
        {
            echo $l_exceed_ore;
        }
        elseif (abs($_POST['trade_goods']) > $portinfo['port_goods'])
        {
            echo $l_exceed_goods;
        }
        elseif (abs($_POST['trade_energy']) > $portinfo['port_energy'])
        {
            echo $l_exceed_energy;
        }
        else
        {
            if ($total_cost == 0 )
            {
                $trade_color   = "white";
                $trade_result  = "$l_cost : ";
            }
            elseif ($total_cost < 0 )
            {
                $trade_color   = $color_green;
                $trade_result  = $trade_benefit;
            }
            else
            {
                $trade_color   = $color_red;
                $trade_result  = $trade_deficit;
            }

            echo "
                  <table border=2 cellspacing=2 cellpadding=2 bgcolor=#400040 width=600 align=center>
                     <tr>
                        <td colspan=99 align=center><font color=white><strong>$l_trade_result</strong></font></td>
                     </tr>
                     <tr>
                        <td colspan=99 align=center><strong><font color=\"". $trade_color . "\">". $trade_result ." " . number_format(abs($total_cost), 0, $local_number_dec_point, $local_number_thousands_sep) . " $l_credits</font></strong></td>
                     </tr>
                     <tr bgcolor=$color_line1>
                        <td><strong><font style=\"font-size: 0.8em;\" color=white>$l_traded_ore: </font></strong></td><td align=right><strong><font style=\"font-size: 0.8em;\" color=white>" . number_format($_POST['trade_ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</font></strong></td>
                     </tr>
                     <tr bgcolor=$color_line2>
                        <td><strong><font style=\"font-size: 0.8em;\" color=white>$l_traded_organics: </font></strong></td><td align=right><strong><font style=\"font-size: 0.8em;\" color=white>" . number_format($_POST['trade_organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</font></strong></td>
                     </tr>
                     <tr bgcolor=$color_line1>
                        <td><strong><font style=\"font-size: 0.8em;\" color=white>$l_traded_goods: </font></strong></td><td align=right><strong><font style=\"font-size: 0.8em;\" color=white>" . number_format($_POST['trade_goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</font></strong></td>
                     </tr>
                     <tr bgcolor=$color_line2>
                        <td><strong><font style=\"font-size: 0.8em;\" color=white>$l_traded_energy: </font></strong></td><td align=right><strong><font style=\"font-size: 0.8em;\" color=white>" . number_format($_POST['trade_energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</font></strong></td>
                     </tr>
                  </table>
                 ";

            if (!isset($_POST['trade_ore']))
            {
                $_POST['trade_ore'] = 0;
            }

            if (!isset($_POST['trade_organics']))
            {
                $_POST['trade_organics'] = 0;
            }

            if (!isset($_POST['trade_goods']))
            {
                $_POST['trade_goods'] = 0;
            }

            if (!isset($_POST['trade_energy']))
            {
                $_POST['trade_energy'] = 0;
            }

            // Update ship cargo, credits and turns
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1, rating=rating+1, credits=credits-? WHERE player_id=?", array($total_cost, $playerinfo['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET ore=ore+?, organics=organics+?, goods=goods+?, energy=energy+? WHERE ship_id=?", array($_POST['trade_ore'], $_POST['trade_organics'], $_POST['trade_goods'], $_POST['trade_energy'], $shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            // Make all trades positive to change port values
            $_POST['trade_ore']        = abs($_POST['trade_ore']);
            $_POST['trade_organics']   = abs($_POST['trade_organics']);
            $_POST['trade_goods']      = abs($_POST['trade_goods']);
            $_POST['trade_energy']     = abs($_POST['trade_energy']);

            // Decrease supply and demand on port
            $debug_query = $db->Execute("UPDATE {$db->prefix}ports SET port_ore=port_ore-?, port_organics=port_organics-?, port_goods=port_goods-?, port_energy=port_energy-? WHERE sector_id=?", array($_POST['trade_ore'], $_POST['trade_organics'], $_POST['trade_goods'], $_POST['trade_energy'], $portinfo['sector_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            echo "$l_trade_complete.<br><br>";
        }
    }
}

//-------------------------------------------------------------------------------------------------

if ($portinfo['port_type'] == "upgrades" || $portinfo['port_type'] == "devices")
{
    echo "<br><br> <a href=port.php>$l_clickme</a> $l_port_returntospecial";
}

echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
