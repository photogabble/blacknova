<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: move.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_move_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

$sector  = (int) filter_input(INPUT_GET, 'sector', FILTER_SANITIZE_NUMBER_INT);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('move', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'));

// Retrieve the user and ship information
$result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);

// Put the player information into the array: "playerinfo"
$playerinfo = $result->fields;

// Check to see if the player has less than one turn available
// and if so return to the main menu
if ($playerinfo['turns'] < 1)
{
    echo $langvars['l_move_turn'] . '<br><br>';
    Bnt\Text::gotoMain($db, $lang, $langvars);
    Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
    die();
}

// Retrieve all the sector information about the current sector
$result2 = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array($playerinfo['sector']));
Bnt\Db::logDbErrors($db, $result2, __LINE__, __FILE__);

// Put the sector information into the array "sectorinfo"
$sectorinfo = $result2->fields;

// Retrive all the warp links out of the current sector
$result3 = $db->Execute("SELECT * FROM {$db->prefix}links WHERE link_start = ?;", array($playerinfo['sector']));
Bnt\Db::logDbErrors($db, $result3, __LINE__, __FILE__);
$i = 0;
$flag = 0;

// Loop through the available warp links to make sure it's a valid move
while (!$result3->EOF)
{
    $row = $result3->fields;
    if ($row['link_dest'] == $sector && $row['link_start'] == $playerinfo['sector'])
    {
        $flag = 1;
    }
    $i++;
    $result3->MoveNext();
}

// Check if there was a valid warp link to move to
if ($flag == 1)
{
    $ok = 1;
    $calledfrom = "move.php";
    include_once './check_fighters.php';
    if ($ok > 0)
    {
        $stamp = date("Y-m-d H:i:s");
        Bnt\LogMove::writeLog($db, $playerinfo['ship_id'], $sector);
        $move_result = $db->Execute("UPDATE {$db->prefix}ships SET last_login = ?,turns = turns - 1, turns_used = turns_used + 1, sector = ? WHERE ship_id = ?;", array($stamp, $sector, $playerinfo['ship_id']));
        Bnt\Db::logDbErrors($db, $move_result, __LINE__, __FILE__);
        if (!$move_result)
        {
            // is this really STILL needed?
            $error = $db->ErrorMsg();
            mail($bntreg->admin_mail, "Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nSQL error: $error");
        }
    }
    // Enter code for checking dangers in new sector
    include_once './check_mines.php';
    if ($ok == 1)
    {
        header("Location: main.php");
    }
    else
    {
        Bnt\Text::gotoMain($db, $lang, $langvars);
    }
}
else
{
    echo $langvars['l_move_failed'] . '<br><br>';
    $resx = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defences=' ' WHERE ship_id = ?;", array($playerinfo['ship_id']));
    Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    Bnt\Text::gotoMain($db, $lang, $langvars);
}

echo "</body></html>";
?>
