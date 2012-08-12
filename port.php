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
// File: port.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'report');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'device');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'spy');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_title_port;
updatecookie($db);
include_once './header.php';

if (!isset($pay))
{
    $pay = '';
}
//-------------------------------------------------------------------------------------------------

// This fixes negative quantites: We don't know how they get that way. Any ideas?

if ($shipinfo['ore'] < 0 )
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ships set ore=0 WHERE ship_id=?", array($shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $shipinfo['ore'] = 0;
}

if ($shipinfo['organics'] < 0 )
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ships set organics=0 WHERE ship_id=?", array($shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $shipinfo['organics'] = 0;
}

if ($shipinfo['energy'] < 0 )
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ships set energy=0 WHERE ship_id=?", array($shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $shipinfo['energy'] = 0;
}

if ($shipinfo['goods'] < 0 )
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ships set goods=0 WHERE ship_id=?", array($shipinfo['ship_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $shipinfo['goods'] = 0;
}

if ($portinfo['port_ore'] < 0 )
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ports set port_ore=0 WHERE sector_id=?", array($shipinfo['sector_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $portinfo['port_ore'] = 0;
}

if ($portinfo['port_goods']<0)
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}uports set port_goods=0 WHERE sector_id=?", array($shipinfo['sector_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $portinfo['port_goods'] = 0;
}

if ($portinfo['port_organics']<0)
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ports set port_organics=0 WHERE sector_id=?", array($shipinfo['sector_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $portinfo['port_organics'] = 0;
}

if ($portinfo['port_energy']<0)
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}ports set port_energy=0 WHERE sector_id=?", array($shipinfo['sector_id']));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $portinfo['port_energy'] = 0;
}

if ($zoneinfo['zone_id'] == 4)
{
    $title = $l_sector_war;
    echo "<h1>" . $title. "</h1>\n";
    echo "$l_war_info <p>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
    $title = $l_no_trade;
    echo "<h1>" . $title. "</h1>\n";
    echo "$l_no_trade_info<p>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
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
            echo "Trading at this port is not allowed for outsiders<p>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once './footer.php';
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
            include_once './footer.php';
            die();
        }
    }
}

//-------------------------------------------------------------------------------------------------

if ($portinfo['port_type'] != "none" && $portinfo['port_type'] != "upgrades" && $portinfo['port_type'] != "devices")
{
    $title = $l_title_trade;
    echo "<h1>" . $title. "</h1>\n";

    if ($portinfo['port_type'] == "ore")
    {
        $ore_price = floor($ore_price - $ore_delta * $portinfo['port_ore'] / $ore_limit * $inventory_factor);
        $sb_ore = $l_selling;
    }
    else
    {
        $ore_price = floor($ore_price + $ore_delta * $portinfo['port_ore'] / $ore_limit * $inventory_factor);
        $sb_ore = $l_buying;
    }

    if ($portinfo['port_type'] == "organics")
    {
        $organics_price = floor($organics_price - $organics_delta * $portinfo['port_organics'] / $organics_limit * $inventory_factor);
        $sb_organics = $l_selling;
    }
    else
    {
        $organics_price = floor($organics_price + $organics_delta * $portinfo['port_organics'] / $organics_limit * $inventory_factor);
        $sb_organics = $l_buying;
    }

    if ($portinfo['port_type'] == "goods")
    {
        $goods_price = floor($goods_price - $goods_delta * $portinfo['port_goods'] / $goods_limit * $inventory_factor);
        $sb_goods = $l_selling;
    }
    else
    {
        $goods_price = floor($goods_price + $goods_delta * $portinfo['port_goods'] / $goods_limit * $inventory_factor);
        $sb_goods = $l_buying;
    }

    if ($portinfo['port_type'] == "energy")
    {
        $energy_price = floor($energy_price - $energy_delta * $portinfo['port_energy'] / $energy_limit * $inventory_factor);
        $sb_energy = $l_selling;
    }
    else
    {
        $energy_price = floor($energy_price + $energy_delta * $portinfo['port_energy'] / $energy_limit * $inventory_factor);
        $sb_energy = $l_buying;
    }

    // establish default amounts for each commodity
    if ($sb_ore == $l_buying)
    {
        $amount_ore = $shipinfo['ore'];
    }
    else
    {
        $amount_ore = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['colonists'];
    }

    if ($sb_organics == $l_buying)
    {
        $amount_organics = $shipinfo['organics'];
    }
    else
    {
        $amount_organics = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['organics'] - $shipinfo['colonists'];
    }

    if ($sb_goods == $l_buying)
    {
        $amount_goods = $shipinfo['goods'];
    }
    else
    {
        $amount_goods = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['goods'] - $shipinfo['colonists'];
    }

    if ($sb_energy == $l_buying)
    {
        $amount_energy = $shipinfo['energy'];
    }
    else
    {
        $amount_energy = (5 * num_level($shipinfo['power'], $level_factor, $level_magnitude)) - $shipinfo['energy'];
    }

    // limit amounts to port quantities
    $amount_ore = min($amount_ore, $portinfo['port_ore']);
    $amount_organics = min($amount_organics, $portinfo['port_organics']);
    $amount_goods = min($amount_goods, $portinfo['port_goods']);
    $amount_energy = min($amount_energy, $portinfo['port_energy']);

    // limit amounts to what the player can afford
    if ($sb_ore == $l_selling)
    {
        $amount_ore = min($amount_ore, floor(($playerinfo['credits'] + $amount_organics * $organics_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $ore_price));
    }

    if ($sb_organics == $l_selling)
    {
        $amount_organics = min($amount_organics, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $organics_price));
    }

    if ($sb_goods == $l_selling)
    {
        $amount_goods = min($amount_goods, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_energy * $energy_price) / $goods_price));
    }

    if ($sb_energy == $l_selling)
    {
        $amount_energy = min($amount_energy, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_goods * $goods_price) / $energy_price));
    }

//    echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean.js\"></script>";

    echo '<form name="bntform" action="port2.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=0>";
    echo "<tr bgcolor=\"$color_header\"><td><strong>$l_commodity</strong></td><td><strong>$l_buying/$l_selling</strong></td><td><strong>$l_amount</strong></td><td><strong>$l_price</strong></td><td><strong>$l_buy/$l_sell</strong></td><td><strong>$l_cargo</strong></td></tr>";
    echo "<tr bgcolor=\"$color_line1\"><td>$l_ore</td><td>$sb_ore</td><td>" . number_format($portinfo['port_ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>$ore_price</td><td><input type=text name=trade_ore SIZE=10 MAXLENGTH=20 value=$amount_ore></td><td>" . number_format($shipinfo['ore'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td>$l_organics</td><td>$sb_organics</td><td>" . number_format($portinfo['port_organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>$organics_price</td><td><input type=text name=trade_organics SIZE=10 MAXLENGTH=20 value=$amount_organics></td><td>" . number_format($shipinfo['organics'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td></tr>";
    echo "<tr bgcolor=\"$color_line1\"><td>$l_goods</td><td>$sb_goods</td><td>" . number_format($portinfo['port_goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>$goods_price</td><td><input type=text name=trade_goods SIZE=10 MAXLENGTH=20 value=$amount_goods></td><td>" . number_format($shipinfo['goods'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td></tr>";
    echo "<tr bgcolor=\"$color_line2\"><td>$l_energy</td><td>$sb_energy</td><td>" . number_format($portinfo['port_energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>$energy_price</td><td><input type=text name=trade_energy SIZE=10 MAXLENGTH=20 value=$amount_energy></td><td>" . number_format($shipinfo['energy'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td></tr>";
    echo "</table><br>";
    echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";
    echo "<input type=submit name=submit_button value=$l_trade onclick=\"clean_forms()\">";
    echo "</form>";

    $free_holds = num_level($shipinfo['hull'], $level_factor, $level_magnitude) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
    $free_power = num_level($shipinfo['power'], $level_factor, $level_magnitude) - $shipinfo['energy'];

    $l_trade_st_info=str_replace("[free_holds]",number_format($free_holds, 0, $local_number_dec_point, $local_number_thousands_sep),$l_trade_st_info);
    $l_trade_st_info=str_replace("[free_power]",number_format($free_power, 0, $local_number_dec_point, $local_number_thousands_sep),$l_trade_st_info);
    $l_trade_st_info=str_replace("[credits]",number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_trade_st_info);

    echo $l_trade_st_info;
}
elseif ($portinfo['port_type'] == "upgrades")
{
    include_once './port_upgrade.php';
}
elseif ($portinfo['port_type'] == "devices")
{
    include_once './port_devices.php';
}
else
{
    echo $l_noport . "!";
}

echo "\n";
echo "<br><br>\n";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
echo "\n";

include_once './footer.php';

?>
