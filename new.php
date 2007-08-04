<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
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
$smarty->assign("templateset", $templateset);
$smarty->assign("game_drop_down", $game_drop_down);
$smarty->assign("title", $title);
$smarty->assign("l_new_closed_message", $l_new_closed_message);
$smarty->assign("account_creation_closed", $account_creation_closed);
$smarty->assign("l_login_email", $l_login_email);
$smarty->assign("l_new_shipname", $l_new_shipname);
$smarty->assign("l_new_pname", $l_new_pname);
$smarty->assign("l_submit", $l_submit);
$smarty->assign("l_gamenum", $l_gamenum);
$smarty->assign("l_reset", $l_reset);
$smarty->assign("l_new_info", $l_new_info);
$smarty->assign("l_login_pw", $l_login_pw);
$smarty->assign("l_login_pw2", $l_login_pw2);
$smarty->display("$templateset/new.tpl");

include_once ("./footer.php"); 
?>
