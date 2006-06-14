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
// File: mines.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'mines');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_mines_title;
updatecookie($db);
include_once ("./header.php");

if (!isset($_POST['op']))
{
    $_POST['op'] = '';
}

//-------------------------------------------------------------------------------------------------

$result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=$shipinfo[sector_id] ");
$defenseinfo = $result3->fields;

// Put the defense information into the array "defenseinfo"
$i = 0;
$total_sector_fighters = 0;
$total_sector_mines = 0;
$owns_all = true;
$fighter_id = 0;
$mine_id = 0;
$set_attack = 'CHECKED';
$set_toll = '';
if ($result3 > 0)
{
    while (!$result3->EOF)
    {
        $defenses[$i] = $result3->fields;
        if ($defenses[$i]['defense_type'] == 'F')
        {
            $total_sector_fighters += $defenses[$i]['quantity'];
        }
        else
        {
            $total_sector_mines += $defenses[$i]['quantity'];
        }

        if ($defenses[$i]['player_id'] != $playerinfo['player_id'])
        {
            $owns_all = false;
        }
        else
        {
            if ($defenses[$i]['defense_type'] == 'F')
            {
                $fighter_id = $defenses[$i]['defense_id'];
                if ($defenses[$i]['fm_setting'] == 'attack')
                {
                    $set_attack = 'CHECKED';
                    $set_toll = '';
                }
                else
                {
                    $set_attack = '';
                    $set_toll = 'CHECKED';
                }

            }
            else
            {
                $mine_id = $defenses[$i]['defense_id'];
            }
        }
        $i++;
        $result3->MoveNext();
    }
}

$num_defenses = $i;
echo "<h1>" . $title. "</h1>\n";
if ($playerinfo['turns'] < 1)
{
    echo "$l_mines_noturn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}

$res = $db->Execute("SELECT allow_defenses, {$db->prefix}universe.zone_id, owner FROM {$db->prefix}zones, {$db->prefix}universe " .
                    "WHERE sector_id=$shipinfo[sector_id] AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id");
$query97 = $res->fields;

if ($query97['allow_defenses'] == 'N')
{
    echo "$l_mines_nopermit<br><br>";
}
else
{
    if ($num_defenses > 0)
    {
        if (!$owns_all)
        {
            $defense_owner = $defenses[0]['player_id'];
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=$defense_owner");
            $fighters_owner = $result2->fields;

            if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
            {
                echo "$l_mines_nodeploy<br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                include_once ("./footer.php");
                die();
            }
        }
    }

    if ($query97['allow_defenses'] == 'L')
    {
        $zone_owner = $query97['owner'];
        $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=$zone_owner");
        $zoneowner_info = $result2->fields;

        if ($zone_owner != $playerinfo['player_id'])
        {
             if ($zoneowner_info['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
             {
                 echo "$l_mines_nopermit<br><br>";
                 global $l_global_mmenu;
                 echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                 include_once ("./footer.php");
                 die();
             }
        }
    }

    if (!isset($_POST['nummines']) or !isset($_POST['numfighters']) or !isset($_POST['mode']))
    {
        $availmines = number_format($shipinfo['torps'], 0, $local_number_dec_point, $local_number_thousands_sep);
        $availfighters = number_format($shipinfo['fighters'], 0, $local_number_dec_point, $local_number_thousands_sep);
        echo '<form name="bntform" action="mines.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
        $l_mines_info1=str_replace("[sector]",$shipinfo['sector_id'], $l_mines_info1);
        $l_mines_info1=str_replace("[mines]",number_format($total_sector_mines, 0, $local_number_dec_point, $local_number_thousands_sep), $l_mines_info1);
        $l_mines_info1=str_replace("[fighters]",number_format($total_sector_fighters, 0, $local_number_dec_point, $local_number_thousands_sep), $l_mines_info1);
        echo "$l_mines_info1<br><br>";
        $l_mines_info2=str_replace("[mines]",$availmines, $l_mines_info2);
        $l_mines_info2=str_replace("[fighters]",$availfighters, $l_mines_info2);
        echo "$l_mines_info2<br>";
        echo "$l_mines_deploy <input type=text name=nummines size=10 maxlength=30 value=$shipinfo[torps]> $l_mines.<br>";
        echo "$l_mines_deploy <input type=text name=numfighters size=10 maxlength=30 value=$shipinfo[fighters]> $l_fighters.<br>";
        echo "$l_mines_fmode <input type=radio name=mode $set_attack value=attack>$l_mines_att";
        echo "<input type=radio name=mode $set_toll value=toll>$l_mines_toll<br>";
        echo "<input name=submit_button type=submit value=$l_submit><input type=reset value=$l_reset><br><br>";
        echo "<input type=hidden name=op value=\"$_POST[op]\">";
        echo "</form>";
    }
    else
    {
        $_POST['nummines'] = preg_replace('/[^0-9]/','',$_POST['nummines']);
        $_POST['numfighters'] = preg_replace('/[^0-9]/','',$_POST['numfighters']);
        if (empty($_POST['nummines'])) 
        {
            $_POST['nummines'] = 0;
        }

        if (empty($_POST['numfighters']))
        {
            $_POST['numfighters'] = 0;
        }

        if ($_POST['nummines'] < 0) 
        {
            $_POST['nummines'] = 0;
        }

        if ($_POST['numfighters'] < 0) 
        {
            $_POST['numfighters'] =0;
        }

        if ($_POST['nummines'] > $shipinfo['torps'])
        {
            echo "$l_mines_notorps<br>";
            $_POST['nummines'] = 0;
        }
        else
        {
            $l_mines_dmines=str_replace("[mines]",$_POST['nummines'], $l_mines_dmines);
            echo "$l_mines_dmines<br>";
        }

        if ($_POST['numfighters'] > $shipinfo['fighters'])
        {
            echo "$l_mines_nofighters.<br>";
            $_POST['numfighters'] = 0;
        }
        else
        {
            $tmp = ($_POST['mode'] == 'toll') ? $l_mines_toll : $l_mines_att;
            $l_mines_dfighter=str_replace("[fighters]",$_POST['numfighters'], $l_mines_dfighter);
            $l_mines_dfighter=str_replace("[mode]",$tmp , $l_mines_dfighter);
            echo "$l_mines_dfighter<br>";
        }

        if ($_POST['numfighters'] > 0)
        {
            if ($fighter_id != 0)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity=quantity + $_POST[numfighters], " .
                                            "fm_setting = '$_POST[mode]' WHERE defense_id = $fighter_id");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                $debug_query = $db->Execute("INSERT INTO {$db->prefix}sector_defense " .
                                            "(player_id, sector_id, defense_type, quantity, fm_setting) values " .
                                            "(?,?,?,?,?)", array($playerinfo['player_id'], $shipinfo['sector_id'], 'F', $_POST['numfighters'], $_POST['mode']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }

        if ($_POST['nummines'] > 0)
        {
            if ($mine_id != 0)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity=quantity + $_POST[nummines], " .
                                            "fm_setting = '$_POST[mode]' WHERE defense_id = $mine_id");
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                $debug_query = $db->Execute("INSERT INTO {$db->prefix}sector_defense " .
                                            "(player_id, sector_id, defense_type, quantity, fm_setting) values " .
                                            "(?,?,?,?,?)", array($playerinfo['player_id'], $shipinfo['sector_id'], 'M', $_POST['nummines'], $_POST['mode']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
        }

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 " .
                                    "WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET fighters=fighters-$_POST[numfighters], torps=torps-$_POST[nummines] WHERE " .
                                    "ship_id=$shipinfo[ship_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
include_once ("./footer.php");

?>
