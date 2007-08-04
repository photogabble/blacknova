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
// File: useredit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/useredit.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

$number_dropdown = 0;
$option_echo = '';
if (empty($_POST['operation']))
{
    $_POST['operation'] = '';
}

if (empty($_POST['user']) && $_POST['operation'] == '')
{
    $res = $db->Execute("select player_id,character_name FROM {$db->prefix}players ORDER BY character_name");
    while (!$res->EOF)
    {
        $row = $res->fields;
        $player_list_id[$number_dropdown] = $row['player_id'];
        $player_list_name[$number_dropdown] = $row['character_name'];

        $res->MoveNext();
        $number_dropdown++;
    }

    $smarty->assign("player_list_id", $player_list_id);
    $smarty->assign("player_list_name", $player_list_name);
}
else
{
    if ($_POST['operation'] == '')
    {
            $res = $db->Execute("SELECT * FROM {$db->prefix}players LEFT JOIN {$db->prefix}ships " .
                                "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                                "LEFT JOIN {$raw_prefix}users ON {$db->prefix}players.account_id = {$raw_prefix}users.account_id " .
                                "WHERE {$db->prefix}players.player_id=$_POST[user]");
            $row = $res->fields;

            $res = $db->Execute("select * from {$db->prefix}ship_types WHERE type_id=$row[class]");
            $shiptypeinfo = $res->fields;

            $res2 = $db->Execute("select * FROM {$db->prefix}ships WHERE player_id=$_POST[user]");
            $row2 = $res2->fields;

            $res4 = $db->Execute("SELECT SUM(amount) as bounty FROM {$db->prefix}bounty WHERE placed_by = 0 AND " .
                                 "bounty_on = $_POST[user]");
            $row4 = $res4->fields;

            if ($row4['bounty'] == '')
            {
                $bbounty = '0';
            }
            else
            {
                $bbounty = $row4['bounty'];
            }

            // Template stuff
            $smarty->assign("account_notes", $row['notes']);
            $smarty->assign("l_ore", $l_ore);
            $smarty->assign("l_organics", $l_organics);
            $smarty->assign("l_goods", $l_goods);
            $smarty->assign("l_energy", $l_energy);
            $smarty->assign("l_colonists", $l_colonists);
            $smarty->assign("character_name", $row['character_name']);
            $smarty->assign("password", '');
            $smarty->assign("email", $row['email']);
            $smarty->assign("user", $_POST['user']);
            $smarty->assign("c_code", $row['c_code']);
            $smarty->assign("shipname", $row2['name']);
            $smarty->assign("ship_class", $shiptypeinfo['type_id']);
            $smarty->assign("destroyed", $row2['destroyed']);
            $smarty->assign("active", $row['active']);
            $smarty->assign("hull", $row2['hull']);
            $smarty->assign("engines", $row2['engines']);
            $smarty->assign("pengines", $row2['pengines']);
            $smarty->assign("power", $row2['power']);
            $smarty->assign("computer", $row2['computer']);
            $smarty->assign("sensors", $row2['sensors']);
            $smarty->assign("armor", $row2['armor']);
            $smarty->assign("shields", $row2['shields']);
            $smarty->assign("beams", $row2['beams']);
            $smarty->assign("torp_launchers", $row2['torp_launchers']);
            $smarty->assign("cloak", $row2['cloak']);
            $smarty->assign("ore", $row2['ore']);
            $smarty->assign("organics", $row2['organics']);
            $smarty->assign("goods", $row2['goods']);
            $smarty->assign("energy", $row2['energy']);
            $smarty->assign("colonists", $row2['colonists']);
            $smarty->assign("fighters", $row2['fighters']);
            $smarty->assign("torps", $row2['torps']);
            $smarty->assign("armor_pts", $row2['armor_pts']);
            $smarty->assign("dev_warpedit", $row2['dev_warpedit']);
            $smarty->assign("dev_genesis", $row2['dev_genesis']);
            $smarty->assign("dev_minedeflector", $row2['dev_minedeflector']);
            $smarty->assign("dev_emerwarp", $row2['dev_emerwarp']);
            $smarty->assign("dev_escapepod", $row2['dev_escapepod']);
            $smarty->assign("dev_fuelscoop", $row2['dev_fuelscoop']);
            $smarty->assign("currentship_id", $row['currentship']);
            $smarty->assign("credits", $row['credits']);
            $smarty->assign("turns", $row['turns']);
            $smarty->assign("turns_used", $row['turns_used']);
            $smarty->assign("sector_id", $row['sector_id']);
            $smarty->assign("bounty", $bbounty);

            $res3 = $db->Execute("select * FROM {$db->prefix}ibank_accounts WHERE player_id=$_POST[user]");
            $row3 = $res3->fields;

            $smarty->assign("igb_balance", $row3['balance']);
            $smarty->assign("igb_loan", $row3['loan']);
            $smarty->assign("igb_loantime", $row3['loantime']);

            $res = $db->Execute("select * FROM {$db->prefix}planets WHERE owner=$_POST[user]");

            $stuff = array();

            while (!$res->EOF)
            {
                $row = $res->fields;

                $name = ($row['name']=="")?("Unnamed"):($row['name']);
                // $id[] = $row[planet_id];
                $sector = $row['sector_id'];
                $stuff[] = "'$name' in sector $sector";

                $res->MoveNext();
            }

            $smarty->assign("stuff", $stuff);
        }
        elseif ($_POST['operation'] == "save")
        {
            if ((!isset($_POST['cleared_defenses'])) || ($_POST['cleared_defenses'] == ''))
            {
                $_POST['cleared_defenses'] = '';
            }

            if ((!isset($_POST['destroyed'])) || ($_POST['destroyed'] == ''))
            {
                $_POST['destroyed'] = '';
            }

            if ((!isset($_POST['active'])) || ($_POST['active'] == ''))
            {
                $_POST['active'] = '';
            }

            if ((!isset($_POST['dev_escapepod'])) || ($_POST['dev_escapepod'] == ''))
            {
                $_POST['dev_escapepod'] = '';
            }

            if ((!isset($_POST['dev_fuelscoop'])) || ($_POST['dev_fuelscoop'] == ''))
            {
                $_POST['dev_fuelscoop'] = '';
            }

            if ((!isset($_POST['turns_used'])) || ($_POST['turns_used'] == ''))
            {
                $_POST['turns_used'] = '';
            }

            if ((!isset($_POST['ship_class'])) || ($_POST['ship_class'] == ''))
            {
                $_POST['ship_class'] = '';
            }

            if ((!isset($_POST['igb_balance'])) || ($_POST['igb_balance'] == ''))
            {
                $_POST['igb_balance'] = '';
            }

            if ((!isset($_POST['igb_loan'])) || ($_POST['igb_loan'] == ''))
            {
                $_POST['igb_loan'] = '';
            }

            if ((!isset($_POST['igb_loantime'])) || ($_POST['igb_loantime'] == ''))
            {
                $_POST['igb_loantime'] = '';
            }

            if ((!isset($_POST['c_code'])) || ($_POST['c_code'] == ''))
            {
                $_POST['c_code'] = '';
            }

            if ((!isset($_POST['account_notes'])) || ($_POST['account_notes'] == ''))
            {
                $_POST['account_notes'] = '';
            }

            // Intercept checkbox values
            $_POST['cleared_defenses'] = ($_POST['cleared_defenses'] == "on") ? 1 : 0;
            $_POST['destroyed'] = ($_POST['destroyed'] == "on") ? "Y" : "N";
            $_POST['active'] = ($_POST['active'] == "on") ? "Y" : "N";
            $_POST['dev_escapepod'] = ($_POST['dev_escapepod'] == "on") ? "Y" : "N";
            $_POST['dev_fuelscoop'] = ($_POST['dev_fuelscoop'] == "on") ? "Y" : "N";

            $_POST['character_name'] = preg_replace ("/[^\w\d\s\.\'\@]/","",$_POST['character_name']);
            $_POST['ship_name'] = preg_replace ("/[^\w\d\s\.\'\@]/","",$_POST['ship_name']);

            $_POST['character_name'] = htmlspecialchars($_POST['character_name'],ENT_QUOTES);
            $_POST['ship_name'] = htmlspecialchars($_POST['ship_name'],ENT_QUOTES);

            if (!get_magic_quotes_gpc())
            {
                $_POST['character_name'] = mysql_escape_string($_POST['character_name']);
                $_POST['ship_name'] = mysql_escape_string($_POST['ship_name']);
            }

            $silent = 0;
            if ($_POST['password2'] == '')
            {
                // If they didnt set the password, do not overwrite it.
                echo "Updating PLAYERS table";
                $query = "UPDATE {$db->prefix}players SET character_name='$_POST[character_name]', " .
                         "credits='$_POST[credits]', turns='$_POST[turns]', " .
                         "turns_used='$_POST[turns_used]' " .
                         "WHERE player_id='$_POST[user]'";
                $debug_query = $db->Execute($query);
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "<br>Updating ACCOUNTS table";
                $query = "UPDATE {$raw_prefix}users SET email='$_POST[email]', notes='$_POST[account_notes]', active ='$_POST[active]', c_code='$_POST[c_code]' " .
                         "WHERE email='$_POST[email]'";
                $debug_query = $db->Execute($query);
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                // If they did set the password, update it.
                echo "<br>Updating PLAYERS table";
                $query = "UPDATE {$db->prefix}players SET character_name='$_POST[character_name]', " .
                         "email='$_POST[email]', credits='$_POST[credits]', turns='$_POST[turns]', " .
                         "active ='$_POST[active]', " .
                         "turns_used='$_POST[turns_used]', password='" . sha1($_POST['password2']) ."' " .
                         "WHERE player_id='$_POST[user]'";
                $debug_query = $db->Execute($query);
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            echo "<br>Updating SHIPS table ";
            $query = "UPDATE {$db->prefix}ships SET ";
            $query .= "class='$_POST[ship_class]', name='$_POST[ship_name]', destroyed='$_POST[destroyed]', " .
                      "hull='$_POST[hull]', engines='$_POST[engines]', pengines='$_POST[pengines]', power='$_POST[power]', " .
                      "computer='$_POST[computer]', sensors='$_POST[sensors]', beams='$_POST[beams]', " .
                      "torp_launchers='$_POST[torp_launchers]', torps='$_POST[torps]', shields='$_POST[shields]', " .
                      "armor='$_POST[armor]', armor_pts='$_POST[armor_pts]', cloak='$_POST[cloak]', " .
                      "sector_id='$_POST[sector]', ore='$_POST[ship_ore]', organics='$_POST[ship_organics]', " .
                      "goods='$_POST[ship_goods]', energy='$_POST[ship_energy]', colonists='$_POST[ship_colonists]', " .
                      "fighters='$_POST[ship_fighters]', dev_warpedit='$_POST[dev_warpedit]', " .
                      "dev_genesis='$_POST[dev_genesis]', dev_emerwarp='$_POST[dev_emerwarp]', " .
                      "dev_escapepod='$_POST[dev_escapepod]', dev_fuelscoop='$_POST[dev_fuelscoop]', " .
                      "dev_minedeflector='$_POST[dev_minedeflector]'";
            $query .= " WHERE player_id='$_POST[user]'";

            // varibles of SHIPS not settable from admin
            // on_planet
            // planet_id

            $debug_query = $db->Execute($query);
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            // Store igb data in db
            echo "<br>Updating IBANK_ACCOUNTS table ";
            $query = "UPDATE {$db->prefix}ibank_accounts SET balance='$_POST[igb_balance]', loan='$_POST[igb_loan]', " .
                     "loantime='$_POST[igb_loantime]' WHERE player_id='$_POST[user]'";
            $debug_query = $db->Execute($query);
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            echo "<br><br>";
            // Set the player's defenses to cleared.
            if ($_POST['cleared_defenses'] != '')
            {
                echo "Updating the cleared defenses ";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=? WHERE player_id=?", array(' ', $_POST['user']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            $silent = 0;
        }
        else
        {
            echo "Invalid operation";
        }
    }

$smarty->assign("post_operation", $_POST['operation']);
$smarty->assign("number_dropdown", $number_dropdown);
$smarty->display("$templateset/admin/useredit.tpl");
?>
