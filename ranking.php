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
// File: ranking.php

include './global_includes.php';

// Always make sure we are using empty vars before use.
$langvars = null;
$variables = null;

$variables['body_class'] = 'ranking';
$variables['lang'] = $lang;
$variables['link'] = "ranking.php";

// These should be set within the template config.
$variables['color_header'] = $color_header;
$variables['color_line1'] = $color_line1;
$variables['color_line2'] = $color_line2;

// Load required language variables for the ranking page.
$langvars = BntTranslate::load ($db, $lang, array ('main', 'ranking', 'common', 'global_includes', 'global_funcs', 'footer', 'teams'));

// Modify the requires language variables here.
$langvars['l_ranks_title'] = str_replace ("[max_ranks]", $max_ranks, $langvars['l_ranks_title']);

// Get requested ranking order.
$sort = '';
if (isset ($_GET['sort']))
{
    $sort = filter_input (INPUT_GET, 'sort', FILTER_SANITIZE_STRING);
}

switch ($sort)
{
    case "turns":
    {
        $by = "turns_used DESC, character_name ASC";
        break;
    }
    case "login":
    {
        $by = "last_login DESC, character_name ASC";
        break;
    }
    case "good":
    {
        $by = "rating DESC, character_name ASC";
        break;
    }
    case "bad":
    {
        $by = "rating ASC, character_name ASC";
        break;
    }
    case "team":
    {
        $by = "{$db->prefix}teams.team_name DESC, character_name ASC";
        break;
    }
    case "efficiency":
    {
        $by = "efficiency DESC";
        break;
    }
    default:
    {
        $by = "score DESC, character_name ASC";
        break;
    }
}

$variables['num_players'] = (integer) 0;

$rs = $db->Execute ("SELECT {$db->prefix}ships.ship_id, {$db->prefix}ships.email, {$db->prefix}ships.ip_address, {$db->prefix}ships.score, {$db->prefix}ships.character_name, {$db->prefix}ships.turns_used, {$db->prefix}ships.last_login,UNIX_TIMESTAMP({$db->prefix}ships.last_login) as online, {$db->prefix}ships.rating, {$db->prefix}teams.team_name, {$db->prefix}teams.admin AS team_admin, if ({$db->prefix}ships.turns_used<150,0,ROUND({$db->prefix}ships.score/{$db->prefix}ships.turns_used)) AS efficiency FROM {$db->prefix}ships LEFT JOIN {$db->prefix}teams ON {$db->prefix}ships.team = {$db->prefix}teams.id  WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' AND turns_used >0 ORDER BY $by LIMIT $max_ranks");
DbOp::dbResult ($db, $rs, __LINE__, __FILE__);
if ($rs instanceof ADORecordSet)
{
    $variables['num_players'] = (integer) $rs->RecordCount();

    if ($variables['num_players'] > 0)
    {
        $player_list = array ();

        while (!$rs->EOF)
        {
            $row = $rs->fields;

            // Set the players rank number.
            $row['rank'] = count ($player_list) + 1;

            // Calculate the players rating.
            $rating = round (sqrt (abs ($row['rating']) ));
            if (abs ($row['rating']) != $row['rating'])
            {
                $rating = -1 * $rating;
            }
            $row['rating']  = $rating;

            // Calculate the players online status.
            $curtime = TIME ();
            $time = $row['online'];
            $difftime = ($curtime - $time) / 60;
            $temp_turns = $row['turns_used'];
            if ($temp_turns <= 0)
            {
                $temp_turns = 1;
            }

            // Set the players online/offline status.
            $row['online'] = (boolean) false;
            if ($difftime <= 5)
            {
                $row['online'] = (boolean) true;
            }

            // Set the players Insignia.
            $row['insignia'] = BntPlayer::getInsignia ($db, $row['email'], $langvars);

            // This is just to show that we can set the type of player.
            // like: banned, admin, player, npc etc.
            if ($row['email'] == $admin_mail || $row['team_admin'] === "Y")
            {
                $row['type'] = "admin";
            }
            else
            {
                $row['type'] = "player";
            }

            // Check for banned players.
            $ban_result = CheckBan::isBanned ($db, $lang, null, $row);
            if ($ban_result === false || (array_key_exists ('ban_type', $ban_result) && $ban_result['ban_type'] === ID_WATCH))
            {
                $row['banned'] = (boolean) false;
                $row['ban_info'] = null;
            }
            else
            {
                $row['banned'] = (boolean) true;
                $row['ban_info'] = array('type'=>$ban_result['ban_type'], 'public_info'=>"Player banned/locked for the following:\n{$ban_result['public_info']}");
            }

            array_push ($player_list, $row);

            $rs->MoveNext ();
        }
        $player_list['container']    = "player";
        $template->AddVariables ("players", $player_list);
    }
}

if (empty ($_SESSION['username']))
{
    $variables['loggedin'] = (boolean) true;
    $variables['linkback'] = array ("caption"=>$langvars['l_global_mlogin'], "link"=>"index.php");
}
else
{
    $variables['loggedin'] = (boolean) false;
    $variables['linkback'] = array ("caption"=>$langvars['l_global_mmenu'], "link"=>"main.php");
}

// Now we include the Footer Logic.
include_once './footer_t.php';

$variables['container'] = "variable";
$template->AddVariables ('variables', $variables);

// Now add the loaded language variables into the Template API.
$langvars['container'] = "langvar";
$template->AddVariables ('langvars', $langvars);

// Now we tell the Template API to output the page
$template->Display("ranking.tpl");
?>
