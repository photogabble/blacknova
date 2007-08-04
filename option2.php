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
// File: option2.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'option2');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'option2');
load_languages($db, $raw_prefix, 'common');

// Include the sha256 backend
include_once ("./backends/sha256/shaclass.php");

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_opt2_title;
updatecookie($db);
include_once ("./header.php");

//-------------------------------------------------------------------------------------------------

if (!isset($_POST['use_gravatar']))
{
    $_POST['use_gravatar'] = '';
}

if (($_POST['newpass1'] == $_POST['newpass2']) && ($accountinfo['password'] == sha256::hash($_POST['oldpass'])) && ($_POST['newpass1'] != ''))
{
    adodb_session_regenerate_id();

    // Get the player's account id
    $res = $db->Execute("SELECT account_id from {$db->prefix}players WHERE player_id=$playerinfo[player_id]");
    db_op_result($db,$res,__LINE__,__FILE__);
    $account_id = $res->fields['account_id'];

    $debug_query = $db->Execute("UPDATE {$raw_prefix}users SET password='$_POST[crypted_password]' WHERE account_id=$account_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $_SESSION['password'] = $_POST['crypted_password'];
}

if ($_POST['ship_name'] != $shipinfo['name'])
{
    $ship_name = htmlspecialchars(trim($_POST['ship_name']),ENT_QUOTES,"UTF-8");
    if ($ship_name != '')
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET name='$ship_name' WHERE ship_id=$shipinfo[ship_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}

if ($_POST['use_gravatar'] != $playerinfo['use_gravatar'])
{
    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET use_gravatar='$_POST[use_gravatar]' WHERE player_id=$playerinfo[player_id]");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}

if ($allow_shoutbox)
{
    if (($_POST['sb_lines'] < 50) && ($_POST['sb_lines'] > 0))
    {
        $sb_lines = $_POST['sb_lines'];
    }
    else
    {
        $sb_lines = $playerinfo['sb_lines'];
    }

    if ($sb_lines != $playerinfo['sb_lines'])
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET sb_lines='$sb_lines' WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    if ($_POST['sb_footer'] != 'Y')
    {
        $sb_footer = 'N';
    }
    else
    {
        $sb_footer = 'Y';
    }

    if ($sb_footer != $playerinfo['sb_footer'])
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET sb_footer='$sb_footer' WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    if ($_POST['sb_backwards'] != 'Y')
    {
        $sb_footer = 'N';
    }
    else
    {
        $sb_footer = 'Y';
    }

    if ($sb_footer != $playerinfo['sb_backwards'])
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET sb_backwards='$sb_backwards' WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}

$maxval = count($avail_lang);

for ($i=0; $i<$maxval; $i++)
{
    if ($avail_lang[$i]['value'] == $langdir)
    {
        $l_opt2_chlang = str_replace("[lang]", $avail_lang[$i]['name'], $l_opt2_chlang);
        break;
    }
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
$smarty->assign("title", $title);
$smarty->assign("l_global_mmenu", $l_global_mmenu);
$smarty->assign("password", $accountinfo['password']);
$smarty->assign("newpass1", $_POST['newpass1']);
$smarty->assign("newpass2", $_POST['newpass2']);
$smarty->assign("l_opt2_passunchanged", $l_opt2_passunchanged);
$smarty->assign("l_opt2_newpassnomatch", $l_opt2_newpassnomatch);
$smarty->assign("accountinfo_password", $accountinfo['password']);
$smarty->assign("oldpass", sha256::hash($_POST['oldpass']));
$smarty->assign("l_opt2_srcpassfalse", $l_opt2_srcpassfalse);
$smarty->assign("debug_query", $debug_query);
$smarty->assign("l_opt2_passchanged", $l_opt2_passchanged);
$smarty->assign("l_opt2_passchangeerr", $l_opt2_passchangeerr);
$smarty->assign("l_opt2_chlang", $l_opt2_chlang);
$smarty->display("$templateset/option2.tpl");

include_once ("./footer.php");
?>
