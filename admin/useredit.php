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

    $template->assign("player_list_id", $player_list_id);
    $template->assign("player_list_name", $player_list_name);
}
else
{
    if ($_POST['operation'] == '')
    {
            $res = $db->Execute("SELECT * FROM {$db->prefix}players LEFT JOIN {$db->prefix}ships " .
                                "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                                "LEFT JOIN {$raw_prefix}users ON {$db->prefix}players.account_id = {$raw_prefix}users.account_id " .
                                "WHERE {$db->prefix}players.player_id=?", array($_POST['user']));
            $row = $res->fields;

            $res = $db->Execute("SELECT * FROM {$db->prefix}ship_types WHERE type_id=?", array($row['class']));
            $shiptypeinfo = $res->fields;

            $res2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE player_id=?", array($_POST['user']));
            $row2 = $res2->fields;

            $res4 = $db->Execute("SELECT SUM(amount) as bounty FROM {$db->prefix}bounty WHERE placed_by = 0 AND " .
                                 "bounty_on =?", array($_POST['user']));
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
            $template->assign("account_notes", $row['notes']);
            $template->assign("l_ore", $l_ore);
            $template->assign("l_organics", $l_organics);
            $template->assign("l_goods", $l_goods);
            $template->assign("l_energy", $l_energy);
            $template->assign("l_colonists", $l_colonists);
            $template->assign("character_name", $row['character_name']);
            $template->assign("password", '');
            $template->assign("email", $row['email']);
            $template->assign("user", $_POST['user']);
            $template->assign("c_code", $row['c_code']);
            $template->assign("shipname", $row2['name']);
            $template->assign("ship_class", $shiptypeinfo['type_id']);
            $template->assign("destroyed", $row2['destroyed']);
            $template->assign("active", $row['active']);
            $template->assign("hull", $row2['hull']);
            $template->assign("engines", $row2['engines']);
            $template->assign("pengines", $row2['pengines']);
            $template->assign("power", $row2['power']);
            $template->assign("computer", $row2['computer']);
            $template->assign("sensors", $row2['sensors']);
            $template->assign("armor", $row2['armor']);
            $template->assign("shields", $row2['shields']);
            $template->assign("beams", $row2['beams']);
            $template->assign("torp_launchers", $row2['torp_launchers']);
            $template->assign("cloak", $row2['cloak']);
            $template->assign("ore", $row2['ore']);
            $template->assign("organics", $row2['organics']);
            $template->assign("goods", $row2['goods']);
            $template->assign("energy", $row2['energy']);
            $template->assign("colonists", $row2['colonists']);
            $template->assign("fighters", $row2['fighters']);
            $template->assign("torps", $row2['torps']);
            $template->assign("armor_pts", $row2['armor_pts']);
            $template->assign("dev_warpedit", $row2['dev_warpedit']);
            $template->assign("dev_genesis", $row2['dev_genesis']);
            $template->assign("dev_minedeflector", $row2['dev_minedeflector']);
            $template->assign("dev_emerwarp", $row2['dev_emerwarp']);
            $template->assign("dev_escapepod", $row2['dev_escapepod']);
            $template->assign("dev_fuelscoop", $row2['dev_fuelscoop']);
            $template->assign("currentship_id", $row['currentship']);
            $template->assign("credits", $row['credits']);
            $template->assign("turns", $row['turns']);
            $template->assign("turns_used", $row['turns_used']);
            $template->assign("sector_id", $row['sector_id']);
            $template->assign("bounty", $bbounty);

            $res3 = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=?", array($_POST['user']));
            $row3 = $res3->fields;

            $template->assign("igb_balance", $row3['balance']);
            $template->assign("igb_loan", $row3['loan']);
            $template->assign("igb_loantime", $row3['loantime']);

            $res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner=?", array($_POST['user']));

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

            $template->assign("stuff", $stuff);
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
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET character_name=?, " .
                         "credits=?, turns=?, " .
                         "turns_used=? " .
                         "WHERE player_id=?", array($_POST['character_name'], $_POST['credits'], $_POST['turns'], $_POST['turns_used'], $_POST['user']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);

                echo "<br>Updating ACCOUNTS table";
                $debug_query = $db->Execute("UPDATE {$raw_prefix}users SET email=?, notes=?, active=?, c_code=? " .
                         "WHERE email=?", array($_POST['email'], $_POST['account_notes'], $_POST['active'], $_POST['c_code'], $_POST['email']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }
            else
            {
                // If they did set the password, update it.
                echo "<br>Updating PLAYERS table";
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET character_name=?, " .
                         "email=?, credits=?, turns=?, " .
                         "active=?, " .
                         "turns_used=?, password=? " .
                         "WHERE player_id=?", array($_POST['character_name'], $_POST['email'], $_POST['credits'], $_POST['turns'], $_POST['active'], $_POST['turns_used'], sha256($_POST['password2']), $_POST['user']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            // varibles of SHIPS not settable from admin
            // on_planet
            // planet_id
            echo "<br>Updating SHIPS table ";
            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET " .
                      "class=?, name=?, destroyed=?, " .
                      "hull=?, engines=?, pengines=?, power=?, " .
                      "computer=?, sensors=?, beams=?, " .
                      "torp_launchers=?, torps=?, shields=?, " .
                      "armor=?, armor_pts=?, cloak=?, " .
                      "sector_id=?, ore=?, organics=?, " .
                      "goods=?, energy=?, colonists=?, " .
                      "fighters=?, dev_warpedit=?, " .
                      "dev_genesis=?, dev_emerwarp=?, " .
                      "dev_escapepod=?, dev_fuelscoop=?, " .
                      "dev_minedeflector=?" .
                      " WHERE player_id=?", array($_POST['ship_class'], $_POST['ship_name'], $_POST['destroyed'], $_POST['hull'], $_POST['engines'], $_POST['pengines'], $_POST['power'], $_POST['computer'], $_POST['sensors'], $_POST['beams'], $_POST['torp_launchers'], $_POST['torps'], $_POST['shields'], $_POST['armor'], $_POST['armor_pts'], $_POST['cloak'], $_POST['sector'], $_POST['ship_ore'], $_POST['ship_organics'], $_POST['ship_goods'], $_POST['ship_energy'], $_POST['ship_colonists'], $_POST['ship_fighters'], $_POST['dev_warpedit'], $_POST['dev_genesis'], $_POST['dev_emerwarp'], $_POST['dev_escapepod'], $_POST['dev_fuelscoop'], $_POST['dev_minedeflector'], $_POST['user']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            // Store igb data in db
            echo "<br>Updating IBANK_ACCOUNTS table ";
            $debug_query = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance=?, loan=?, " .
                       "loantime=? WHERE player_id=?", array($_POST['igb_balance'], $_POST['igb_loan'], $_POST['igb_loantime'], $_POST['user']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            echo "<br><br>";
            // Set the player's defenses to cleared.
            if ($_POST['cleared_defenses'] != '')
            {
                echo "Updating the cleared defenses ";
                $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE player_id=?", array($_POST['user']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
            }

            $silent = 0;
        }
        else
        {
            echo "Invalid operation";
        }
    }

$template->assign("post_operation", $_POST['operation']);
$template->assign("number_dropdown", $number_dropdown);
$template->display("$templateset/admin/useredit.tpl");
?>
