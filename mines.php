<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: mines.php

include './global_includes.php';

if (check_login ($db, $lang, $langvars)) // Checks player login, sets playerinfo
{
    die();
}

// New database driven language entries
load_languages($db, $lang, array('mines', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'), $langvars);

$title = $l_mines_title;
include './header.php';

$op = null;
if (array_key_exists('op', $_GET) == true)
{
    $op = $_GET['op'];
}
elseif(array_key_exists('op', $_POST) == true)
{
    $op = $_POST['op'];
}

$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email=?", array($_SESSION['username']));
db_op_result ($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

$res = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=$playerinfo[sector]");
db_op_result ($db, $res, __LINE__, __FILE__);
$sectorinfo = $res->fields;

$result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=$playerinfo[sector] ");
db_op_result ($db, $result3, __LINE__, __FILE__);

// Put the defence information into the array "defenceinfo"
$i = 0;
$total_sector_fighters = 0;
$total_sector_mines = 0;
$owns_all = true;
$fighter_id = 0;
$mine_id = 0;
$set_attack = 'CHECKED';
$set_toll = '';

# Do we have a valid recordset?
if ($result3 instanceof ADORecordSet)
{
    while (!$result3->EOF)
    {
        $defences[$i] = $result3->fields;;
        if ($defences[$i]['defence_type'] == 'F')
        {
            $total_sector_fighters += $defences[$i]['quantity'];
        }
        else
        {
            $total_sector_mines += $defences[$i]['quantity'];
        }

        if ($defences[$i]['ship_id'] != $playerinfo['ship_id'])
        {
            $owns_all = false;
        }
        else
        {
            if ($defences[$i]['defence_type'] == 'F')
            {
                $fighter_id = $defences[$i]['defence_id'];
                if ($defences[$i]['fm_setting'] == 'attack')
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
                $mine_id = $defences[$i]['defence_id'];
            }
        }
        $i++;
        $result3->MoveNext();
    }
}

$num_defences = $i;
bigtitle ();
if ($playerinfo['turns'] < 1 )
{
    echo $l_mines_noturn . "<br><br>";
    TEXT_GOTOMAIN ();
    include './footer.php';
    die ();
}

$res = $db->Execute("SELECT allow_defenses,{$db->prefix}universe.zone_id,owner FROM {$db->prefix}zones,{$db->prefix}universe WHERE sector_id=$playerinfo[sector] AND {$db->prefix}zones.zone_id={$db->prefix}universe.zone_id");
db_op_result ($db, $res, __LINE__, __FILE__);
$zoneinfo = $res->fields;

if ($zoneinfo['allow_defenses'] == 'N')
{
    echo $l_mines_nopermit . "<br><br>";
}
else
{
    if ($num_defences > 0)
    {
        if (!$owns_all)
        {
            $defence_owner = $defences[0]['ship_id'];
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=$defence_owner");
            db_op_result ($db, $result2, __LINE__, __FILE__);
            $fighters_owner = $result2->fields;

            if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
            {
                echo $l_mines_nodeploy . "<br>";
                TEXT_GOTOMAIN();
                die();
            }
        }
    }

    if ($zoneinfo['allow_defenses'] == 'L')
    {
        $zone_owner = $zoneinfo['owner'];
        $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=$zone_owner");
        db_op_result ($db, $result2, __LINE__, __FILE__);
        $zoneowner_info = $result2->fields;

        if ($zone_owner != $playerinfo['ship_id'])
        {
            if ($zoneowner_info['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
            {
                echo "$l_mines_nopermit<br><br>";
                TEXT_GOTOMAIN();
                die();
            }
        }
    }

    if (!isset($nummines) || !isset($numfighters) || !isset($mode))
    {
        $availmines = NUMBER ($playerinfo['torps']);
        $availfighters = NUMBER ($playerinfo['ship_fighters']);
        echo "<form action=mines.php method=post>";
        $l_mines_info1 = str_replace("[sector]", $playerinfo['sector'], $l_mines_info1);
        $l_mines_info1 = str_replace("[mines]", NUMBER ($total_sector_mines), $l_mines_info1);
        $l_mines_info1 = str_replace("[fighters]", NUMBER ($total_sector_fighters), $l_mines_info1);
        echo "$l_mines_info1<br><br>";
        $l_mines_info2 = str_replace("[mines]", $availmines, $l_mines_info2);
        $l_mines_info2 = str_replace("[fighters]", $availfighters, $l_mines_info2);
        echo "You have $availmines mines and $availfighters fighters available to deploy.<br>\n";
        echo "<br />\n";
        echo "$l_mines_deploy <input type=text name=nummines size=10 maxlength=10 value=$playerinfo[torps]> $l_mines.<br>";
        echo "$l_mines_deploy <input type=text name=numfighters size=10 maxlength=10 value=$playerinfo[ship_fighters]> $l_fighters.<br>";
        echo "Fighter mode <input type=radio name=mode $set_attack value=attack>$l_mines_att</input>";
        echo "<input type=radio name=mode $set_toll value=toll>$l_mines_toll</input><br>";
         echo "<br />\n";
        echo "<input type=submit value=$l_submit><input type=reset value=$l_reset><br><br>";
        echo "<input type=hidden name=op value=$op>";
        echo "</form>";
    }
    else
    {
        $nummines = preg_replace('/[^0-9]/', '', $nummines);
        $numfighters = preg_replace('/[^0-9]/', '', $numfighters);
        if (empty ($nummines)) $nummines = 0;
        if (empty ($numfighters)) $numfighters = 0;
        if ($nummines < 0) $nummines = 0;
        if ($numfighters < 0) $numfighters = 0;
        if ($nummines > $playerinfo['torps'])
        {
            echo $l_mines_notorps . "<br>";
            $nummines = 0;
        }
        else
        {
            $l_mines_dmines=str_replace("[mines]", $nummines, $l_mines_dmines);
            echo $l_mines_dmines . "<br>";
        }

        if ($numfighters > $playerinfo['ship_fighters'])
        {
            echo $l_mines_nofighters . ".<br>";
            $numfighters = 0;
        }
        else
        {
            $l_mines_dfighter = str_replace("[fighters]", $numfighters, $l_mines_dfighter);
            $l_mines_dfighter = str_replace("[mode]", $mode, $l_mines_dfighter);
            echo "$l_mines_dfighter<br>";
        }

        $stamp = date("Y-m-d H-i-s");
        if ($numfighters > 0)
        {
            if ($fighter_id != 0)
            {
                $update = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity=quantity + $numfighters,fm_setting = '$mode' WHERE defence_id = $fighter_id");
                db_op_result ($db, $update, __LINE__, __FILE__);
            }
            else
            {
                $update = $db->Execute("INSERT INTO {$db->prefix}sector_defence (ship_id,sector_id,defence_type,quantity,fm_setting) values ($playerinfo[ship_id],$playerinfo[sector],'F',$numfighters,'$mode')");
                db_op_result ($db, $update, __LINE__, __FILE__);
                echo $db->ErrorMsg();
            }
        }

        if ($nummines > 0)
        {
            if ($mine_id != 0)
            {
                $update = $db->Execute("UPDATE {$db->prefix}sector_defence SET quantity=quantity + $nummines,fm_setting = '$mode' WHERE defence_id = $mine_id");
                db_op_result ($db, $update, __LINE__, __FILE__);
            }
            else
            {
                $update = $db->Execute("INSERT INTO {$db->prefix}sector_defence (ship_id,sector_id,defence_type,quantity,fm_setting) values ($playerinfo[ship_id],$playerinfo[sector],'M',$nummines,'$mode')");
                db_op_result ($db, $update, __LINE__, __FILE__);
            }
        }

        $update = $db->Execute("UPDATE {$db->prefix}ships SET last_login='$stamp',turns=turns-1,turns_used=turns_used+1,ship_fighters=ship_fighters-$numfighters,torps=torps-$nummines WHERE ship_id=$playerinfo[ship_id]");
        db_op_result ($db, $update, __LINE__, __FILE__);
    }
}

TEXT_GOTOMAIN ();
include './footer.php';
?>
