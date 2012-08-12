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
// File: modify_defenses.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "message_defense_owner.php");
dynamic_loader ($db, "explode_mines.php");
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'modify_defenses');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_md_title;
updatecookie($db);

if (isset($_GET['defense_id']))
{
    $defense_id = $_GET['defense_id'];
}
elseif (isset($_POST['defense_id']))
{
    $defense_id = $_POST['defense_id'];
}
else
{
    echo "$l_md_invalid<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

//-------------------------------------------------------------------------------------------------

if (!isset($_POST['response']))
{
    $_POST['response'] = '';
}

if ($playerinfo['turns']<1)
{
    echo "$l_md_noturn<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$result3 = $db->SelectLimit("SELECT * FROM {$db->prefix}sector_defense WHERE defense_id=? AND sector_id=?",1,-1,array($defense_id, $sectorinfo['sector_id']));
// Put the defense information into the array "defenseinfo"
if ($result3 == 0)
{
    echo "$l_md_nolonger<br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once './footer.php';
    die();
}

$defenseinfo = $result3->fields;
if ($defenseinfo['player_id'] == $playerinfo['player_id'])
{
    $defense_owner = $l_md_you;
}
else
{
    $defense_player_id = $defenseinfo['player_id'];
    $resulta = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE player_id=?",1,-1, array($defense_player_id));
    $ownerinfo = $resulta->fields;
    $defense_owner = $ownerinfo['character_name'];
}

$defense_type = $defenseinfo['defense_type'] == 'F' ? $l_fighters : $l_mines;
$qty = $defenseinfo['quantity'];
if ($defenseinfo['fm_setting'] == 'attack')
{
    $set_attack = 'CHECKED';
    $set_toll = '';
}
else
{
    $set_attack = '';
    $set_toll = 'CHECKED';
}

switch ($_POST['response'])
{
    case "fight":
        echo "<h1>" . $title. "</h1>\n";
        if ($defenseinfo['player_id'] == $playerinfo['player_id'])
        {
            echo "$l_md_yours<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once './footer.php';
            die();
        }

        $sector = $shipinfo['sector_id'] ;
        if ($defenseinfo['defense_type'] == 'F')
        {
            $countres = $db->Execute("SELECT SUM(quantity) as totalfighters FROM {$db->prefix}sector_defense WHERE sector_id=? and defense_type='F'", array($sector));
            $ttl = $countres->fields;
            $total_sector_fighters = $ttl['totalfighters'];
            include_once './sector_fighters.php';
        }
        else
        {
            // Attack mines goes here
            $countres = $db->Execute("SELECT SUM(quantity) as totalmines FROM {$db->prefix}sector_defense WHERE sector_id=? and defense_type = 'M'", array($sector));
            $ttl = $countres->fields;
            $total_sector_mines = $ttl['totalmines'];

            $playerbeams = num_level($shipinfo['beams'], $level_factor, $level_magnitude);
            if ($playerbeams > $shipinfo['energy'])
            {
                $playerbeams = $shipinfo['energy'];
            }

            if ($playerbeams > $total_sector_mines)
            {
                $playerbeams = $total_sector_mines;
            }

            echo "$l_md_bmines $playerbeams $l_mines<br>";
            $debug_query = $db->Execute ("UPDATE {$db->prefix}ships SET energy=energy-? WHERE " .
                                        "ship_id=?", array($playerbeams, $shipinfo['ship_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            explode_mines($db,$sector,$playerbeams);
            $char_name = $playerinfo['character_name'];
            $l_md_msgdownerb=str_replace("[sector]",$sector,$l_md_msgdownerb);
            $l_md_msgdownerb=str_replace("[mines]",$playerbeams,$l_md_msgdownerb);
            $l_md_msgdownerb=str_replace("[name]",$char_name,$l_md_msgdownerb);
            message_defense_owner($db, $sector,"$l_md_msgdownerb");
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once './footer.php';
            die();
        }
        break;

    case "retrieve":
        if ($defenseinfo['player_id'] != $playerinfo['player_id'])
        {
            echo "$l_md_notyours<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once './footer.php';
            die();
        }

        $_POST['quantity'] = preg_replace('/[^0-9]/','',$_POST['quantity']);
        if ($_POST['quantity'] < 0)
        {
            $_POST['quantity'] = 0;
        }

        if ($_POST['quantity'] > $defenseinfo['quantity'])
        {
            $_POST['quantity'] = $defenseinfo['quantity'];
        }

        $torpedo_max = num_level($shipinfo['torp_launchers'], $level_factor, $level_magnitude) - $shipinfo['torps'];
        $fighter_max = num_level($shipinfo['computer'], $level_factor, $level_magnitude) - $shipinfo['fighters'];
        if ($defenseinfo['defense_type'] == 'F')
        {
            if ($_POST['quantity'] > $fighter_max)
            {
                $_POST['quantity'] = $fighter_max;
            }
        }

        if ($defenseinfo['defense_type'] == 'M')
        {
            if ($_POST['quantity'] > $torpedo_max)
            {
                $_POST['quantity'] = $torpedo_max;
            }
        }

        $ship_id = $shipinfo['ship_id'];
        if ($_POST['quantity'] > 0)
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense SET quantity=quantity-? WHERE " .
                                        "defense_id=?", array($_POST['quantity'], $defense_id));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            if ($defenseinfo['defense_type'] == 'M')
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships set torps=torps +? WHERE ship_id=?",array($_POST['quantity'], $ship_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships set fighters=fighters+? WHERE ship_id=?", array($_POST['quantity'], $ship_id));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE quantity <= 0");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 " .
                                    "WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id=? WHERE " .
                                    "player_id=?", array($shipinfo['sector_id'],$playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        echo "<h1>" . $title. "</h1>\n";
        echo "$l_md_retr $_POST[quantity] $defense_type.<br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once './footer.php';
        die();
        break;

    case "change":
        echo "<h1>" . $title. "</h1>\n";
        if ($defenseinfo['player_id'] != $playerinfo['player_id'])
        {
            echo "$l_md_notyours<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once './footer.php';
            die();
        }

        $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense SET fm_setting =? WHERE defense_id=?",array($_POST['mode'], $defense_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 " .
                                    "WHERE player_id=?", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id=? WHERE ship_id=?", array($shipinfo['sector_id'], $shipinfo['ship_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if ($_POST['mode'] == 'attack')
        {
            $_POST['mode'] = $l_md_attack;
        }
        else
        {
            $_POST['mode'] = $l_md_toll;
        }

        $l_md_mode=str_replace("[mode]",$_POST['mode'],$l_md_mode);
        echo "$l_md_mode<br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once './footer.php';
        die();
        break;

    default:
        echo "<h1>" . $title. "</h1>\n";
        $l_md_consist=str_replace("[qty]",$qty,$l_md_consist);
        $l_md_consist=str_replace("[type]",$defense_type,$l_md_consist);
        $l_md_consist=str_replace("[owner]",$defense_owner,$l_md_consist);
        echo "$l_md_consist<br>";

        if ($defenseinfo['player_id'] == $playerinfo['player_id'])
        {
            echo "$l_md_youcan:<br>";
            echo "<form action=modify_defenses.php method=post>";
            echo "$l_md_retrieve <input type=text name=quantity size=10 maxlength=10 value=$qty> $defense_type<br>";
            echo "<input type=hidden name=response value=retrieve>";
            echo "<input type=hidden name=defense_id value=$defense_id>";
            echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";
            echo "<input type=submit value=$l_submit onclick=clean_forms()><br><br>";
//            echo "<input type=submit value=$l_submit><br><br>";
            echo "</form>";
            if ($defenseinfo['defense_type'] == 'F')
            {
                echo "$l_md_change:<br>";
                echo "<form action=modify_defenses.php method=post>";
                echo "$l_md_cmode <input type=radio name=mode $set_attack value=attack>$l_md_attack";
                echo "<input type=radio name=mode $set_toll value=toll>$l_md_toll<br>";
                echo "<input type=submit value=$l_submit ><br><br>";
                echo "<input type=hidden name=response value=change>";
                echo "<input type=hidden name=defense_id value=$defense_id>";
                echo "</form>";
            }
        }
        else
        {
            $player_id = $defenseinfo['player_id'];
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($player_id));
            $fighters_owner = $result2->fields;

            if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
            {
                echo "<form action=modify_defenses.php method=post>";
                echo "$l_md_attdef<br><input type=submit value=$l_md_attack><br>";
                echo "<input type=hidden name=response value=fight>";
                echo "<input type=hidden name=defense_id value=$defense_id>";
                echo "</form>";
            }
        }

        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once './footer.php';
        die();
        break;
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once './footer.php';

?>
