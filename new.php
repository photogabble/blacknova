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
// File: new.php

include_once ("./global_includes.php"); 

if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
{
    $add_slash_to_url = '/';
}

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'new');
load_languages($db, $raw_prefix, 'login');

$no_body = 1;

$title = $l_new_title;
include_once ("./header.php");

$game_drop_down='';
$game_query = $db->Execute("SELECT gamenumber FROM {$db->prefix}instances ORDER BY gamenumber ASC");
db_op_result($db,$game_query,__LINE__,__FILE__);

for ($i=1; $i<3; $i++) // Two is the number of game instances right now.
{
    if ($i == 1)
    {
        $selected = " selected=\"selected\"";
    }
    else
    {
        $selected = "";
    }

    $game_drop_down = $game_drop_down . "<option value=" . $i . "$selected>" . "Game #" . $i . "</option>\n";
}

global $account_creation_closed;
$template->assign("templateset", $templateset);
$template->assign("game_drop_down", $game_drop_down);
$template->assign("title", $title);
$template->assign("l_new_closed_message", $l_new_closed_message);
$template->assign("account_creation_closed", $account_creation_closed);
$template->assign("l_login_email", $l_login_email);
$template->assign("l_new_shipname", $l_new_shipname);
$template->assign("l_new_pname", $l_new_pname);
$template->assign("l_submit", $l_submit);
$template->assign("l_gamenum", $l_gamenum);
$template->assign("l_reset", $l_reset);
$template->assign("l_new_info", $l_new_info);
$template->assign("l_login_pw", $l_login_pw);
$template->assign("l_login_pw2", $l_login_pw2);
$template->display("$templateset/new.tpl");

include_once ("./footer.php"); 
?>
