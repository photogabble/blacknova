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
// File: admin/ai_edit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/ai_edit.php"));
if ($pos !== false)
{
    include_once './global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once './header.php';
    echo $l_cannot_access;
    include_once './footer.php';
    die();
}

echo "<span style=\"font-size: 12pt; color: #0F0 \">" . $ai_name . " Editor</span><br>";
echo "<form action=admin.php method=post>";
if (empty($user1))
{
    echo "<SELECT SIZE=20 NAME=user1>";
    $res = $db->Execute("SELECT email,character_name,destroyed,active,sector_id FROM {$db_prefix}players JOIN {$db_prefix}ai LEFT JOIN {$db_prefix}ships ON {$db_prefix}players.player_id = {$db_prefix}ships.player_id WHERE email=ai_id ORDER BY sector_id");
    while (!$res->EOF)
    {
        $row=$res->fields;
        $charnamelist = sprintf("%-20s", $row['character_name']);
        $charnamelist = str_replace("  ", "&nbsp;&nbsp;",$charnamelist);
        $sectorlist = sprintf("Sector %'04d&nbsp;&nbsp;", $row['sector_id']);
        if ($row['active'] == "Y")
        {
            $activelist = "Active &Oslash;&nbsp;&nbsp;";
        }
        else
        {
            $activelist = "Active O&nbsp;&nbsp;";
        }

        if ($row['destroyed'] == "Y")
        {
            $destroylist = "Destroyed &Oslash;&nbsp;&nbsp;";
        }
        else
        {
            $destroylist = "Destroyed O&nbsp;&nbsp;";
        }

        printf ("<OPTION VALUE=%s>%s %s %s %s</OPTION>", $row['email'], $activelist, $destroylist, $sectorlist, $charnamelist);
        $res->MoveNext();
    }

    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ai>";
    echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
}
else
{
    if (empty($operation))
    {
        $res = $db->Execute("SELECT * FROM {$db_prefix}players JOIN {$db_prefix}ai LEFT JOIN {$db_prefix}ships ON {$db_prefix}players.player_id = {$db_prefix}ships.player_id WHERE email=ai_id AND email=?", array($user1));
        $row = $res->fields;
        echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TR><TD>" . $ai_name . " name</TD><TD><INPUT TYPE=TEXT NAME=character_name VALUE=\"$row[character_name]\"></TD></TR>";
        echo "<TR><TD>Active?</TD><TD><INPUT TYPE=CHECKBOX NAME=active VALUE=ON " . CHECKED($row['active']) . "></TD></TR>";
        echo "<TR><TD>E-mail</TD><TD>$row[email]</TD></TR>";
        echo "<TR><TD>ID</TD><TD>$row[player_id]</TD></TR>";
        echo "<TR><TD>Ship</TD><TD><INPUT TYPE=TEXT NAME=ship_name VALUE=\"$row[name]\"></TD></TR>";
        echo "<TR><TD>Destroyed?</TD><TD><INPUT TYPE=CHECKBOX NAME=ship_destroyed VALUE=ON " . CHECKED($row['destroyed']) . "></TD></TR>";
        echo "<TR><TD>Orders</TD><TD>";
        echo "<SELECT SIZE=1 NAME=orders>";
        $oorder0 = $oorder1 = $oorder2 = $oorder3 = "VALUE";
        if ($row[orders] == 0)
        {
            $oorder0 = "SELECTED=0 VALUE";
        }

        if ($row[orders] == 1)
        {
            $oorder1 = "SELECTED=1 VALUE";
        }

        if ($row[orders] == 2)
        {
            $oorder2 = "SELECTED=2 VALUE";
        }

        if ($row[orders] == 3)
        {
            $oorder3 = "SELECTED=3 VALUE";
        }

        echo "<OPTION $oorder0=0>Sentinel</OPTION>";
        echo "<OPTION $oorder1=1>Roam</OPTION>";
        echo "<OPTION $oorder2=2>Roam and Trade</OPTION>";
        echo "<OPTION $oorder3=3>Roam and Hunt</OPTION>";
        echo "</SELECT></TD></TR>";
        echo "<TR><TD>Aggression</TD><TD>";
        $oaggr0 = $oaggr1 = $oaggr2 = "VALUE";
        if ($row['aggression'] == 0)
        {
            $oaggr0 = "SELECTED=0 VALUE";
        }

        if ($row['aggression'] == 1)
        {
            $oaggr1 = "SELECTED=1 VALUE";
        }

        if ($row['aggression'] == 2)
        {
            $oaggr2 = "SELECTED=2 VALUE";
        }

        echo "<SELECT SIZE=1 NAME=aggression>";
        echo "<OPTION $oaggr0=0>Peaceful</OPTION>";
        echo "<OPTION $oaggr1=1>Attack Sometimes</OPTION>";
        echo "<OPTION $oaggr2=2>Attack Always</OPTION>";
        echo "</SELECT></TD></TR>";
        echo "<TR><TD>Levels</TD>";
        echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TR><TD>Hull</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=hull VALUE=\"$row[hull]\"></TD>";
        echo "<TD>Engines</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=engines VALUE=\"$row[engines]\"></TD>";
        echo "<TD>Plasma Engines</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=engines VALUE=\"$row[pengines]\"></TD>";
        echo "<TD>Power</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=power VALUE=\"$row[power]\"></TD>";
        echo "<TD>Computer</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=computer VALUE=\"$row[computer]\"></TD></TR>";
        echo "<TR><TD>Sensors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=sensors VALUE=\"$row[sensors]\"></TD>";
        echo "<TD>armor</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=armor VALUE=\"$row[armor]\"></TD>";
        echo "<TD>Shields</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=shields VALUE=\"$row[shields]\"></TD>";
        echo "<TD>Beams</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=beams VALUE=\"$row[beams]\"></TD></TR>";
        echo "<TR><TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=torp_launchers VALUE=\"$row[torp_launchers]\"></TD>";
        echo "<TD>Cloak</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=cloak VALUE=\"$row[cloak]\"></TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>Holds</TD>";
        echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TR><TD>Ore</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_ore VALUE=\"$row[ore]\"></TD>";
        echo "<TD>Organics</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_organics VALUE=\"$row[organics]\"></TD>";
        echo "<TD>Goods</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_goods VALUE=\"$row[goods]\"></TD></TR>";
        echo "<TR><TD>Energy</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_energy VALUE=\"$row[energy]\"></TD>";
        echo "<TD>Colonists</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_colonists VALUE=\"$row[colonists]\"></TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>Combat</TD>";
        echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TR><TD>Fighters</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_fighters VALUE=\"$row[fighters]\"></TD>";
        echo "<TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=torps VALUE=\"$row[torps]\"></TD></TR>";
        echo "<TR><TD>armor Pts</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=armor_pts VALUE=\"$row[armor_pts]\"></TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>Devices</TD>";
        echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
        echo "<TD>Warp Editors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_warpedit VALUE=\"$row[dev_warpedit]\"></TD>";
        echo "<TD>Genesis Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_genesis VALUE=\"$row[dev_genesis]\"></TD></TR>";
        echo "<TR><TD>Mine Deflectors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_minedeflector VALUE=\"$row[dev_minedeflector]\"></TD>";
        echo "<TD>Emergency Warp</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_emerwarp VALUE=\"$row[dev_emerwarp]\"></TD></TR>";
        echo "<TR><TD>Escape Pod</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_escapepod VALUE=ON " . CHECKED($row['dev_escapepod']) . "></TD>";
        echo "<TD>FuelScoop</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_fuelscoop VALUE=ON " . CHECKED($row['dev_fuelscoop']) . "></TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>Credits</TD><TD><INPUT TYPE=TEXT NAME=credits VALUE=\"$row[credits]\"></TD></TR>";
        echo "<TR><TD>Turns</TD><TD><INPUT TYPE=TEXT NAME=turns VALUE=\"$row[turns]\"></TD></TR>";
        echo "<TR><TD>Current sector</TD><TD><INPUT TYPE=TEXT NAME=sector VALUE=\"$row[sector_id]\"></TD></TR>";
        echo "</TABLE>";
        echo "<br>";
        echo "<INPUT TYPE=HIDDEN NAME=user1 VALUE=$user1>";
        echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
        echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ai>";
        echo "<INPUT TYPE=SUBMIT VALUE=Save>";
        // SHOW AI LOG DATA
        echo "<HR>";
        echo "<span style=\"font-size: 12pt; color: #0F0;\">Log Data For This " . $ai_player . "</span><br>";

        $logres = $db->Execute("SELECT * FROM {$db_prefix}logs WHERE player_id=? ORDER BY time DESC, type DESC", array($row['player_id']));
        while (!$logres->EOF)
        {
            $logrow = $logres->fields;
            $logtype = "";
            switch ($logrow['type'])
            {
                case 'LOG_AI_ATTACK':
                     $logtype = "Launching an attack on ";
                     break;
                case 'LOG_ATTACK_LOSE':
                     $logtype = "We were attacked and lost against ";
                     break;
                case 'LOG_ATTACK_WIN':
                     $logtype = "We were attacked and won against ";
                     break;
            }

            $logdatetime = substr($logrow['time'], 4, 2) . "/" . substr($logrow['time'], 6, 2) . "/" . substr($logrow['time'], 0, 4) . " " . substr($logrow['time'], 8, 2) . ":" . substr($logrow['time'], 10, 2) . ":" . substr($logrow['time'], 12, 2);
            echo "$logdatetime $logtype$logrow[data] <br>";
            $logres->MoveNext();
        }
    }
    elseif ($operation == "save")
    {
        // update database
        $_ship_destroyed = empty($ship_destroyed) ? "N" : "Y";
        $_dev_escapepod = empty($dev_escapepod) ? "N" : "Y";
        $_dev_fuelscoop = empty($dev_fuelscoop) ? "N" : "Y";
        $_active = empty($active) ? "N" : "Y";
        $res = $db->Execute("SELECT ship_id FROM {$db_prefix}players LEFT JOIN {$db_prefix}ships ".
                            "ON {$db_prefix}players.player_id={$db_prefix}ships.player_id WHERE " .
                            "email=?", array($user1));
        $ship_id = $res->fields['ship_id'];

        $result = $db->Execute("UPDATE {$db_prefix}players SET character_name=?, credits=?, turns=? WHERE email=?", array($character_name, $credits, $turns, $user1));
        $result = $db->Execute("UPDATE {$db_prefix}ships SET name=?, destroyed=?, hull=?, engines=?, pengines=?, power=?, computer=?, sensors=?, armor=?, shields=?, beams=?, torp_launchers=?, cloak=?, dev_warpedit=?, dev_genesis=?, dev_emerwarp=?, dev_escapepod=?, dev_fuelscoop=?, dev_minedeflector=?, sector_id=?, ore=?, organics=?, goods=?, energy=?, colonists=?, fighters=?, torps=?, armor_pts=? WHERE ship_id=?",array($ship_name, $_ship_destroyed, $hull, $engines, $pengines, $power, $computer, $sensors, $armor, $shields, $beams, $torp_launchers, $cloak, $dev_warpedit, $dev_genesis, $dev_emerwarp, $_dev_escapepod, $_dev_fuelscoop, $dev_minedeflector, $sector, $ship_ore, $ship_organics, $ship_goods, $ship_energy, $ship_colonists, $ship_fighters, $torps, $armor_pts, $ship_id));
        if (!$result)
        {
            echo "Changes to " . $ai_name . " ship record have FAILED Due to the following Error:<br><br>";
            echo $db->ErrorMsg() . "<br>";
        }
        else
        {
            echo "Changes to " . $ai_name . " ship record have been saved.<br><br>";
            $result2 = $db->Execute("UPDATE {$db_prefix}ai SET active=?, orders=?, aggression=? WHERE ai_id=?", array($_active, $orders, $aggression, $user1));
            if (!$result2)
            {
                echo "Changes to " . $ai_name . " activity record have FAILED Due to the following Error:<br><br>";
                echo $db->ErrorMsg() . "<br>";
            }
            else
            {
                echo "Changes to " . $ai_name . " activity record have been saved.<br><br>";
            }
        }
    }
    else
    {
        echo "Invalid operation";
    }
}

echo "<INPUT TYPE=HIDDEN NAME=module VALUE=ai_edit>";
echo "</form>";

?>
