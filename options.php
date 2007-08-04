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
// File: options.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'option');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_opt_title;
updatecookie($db);
include_once ("./header.php");

if ((!isset($i)) || ($i == ''))
{
    $i = 0;
}

$lang_drop_down = '';

$maxval = count($avail_lang);
for ($i=0; $i<$maxval; $i++)
{
    if ($avail_lang[$i]['value'] == $langdir)
    {
        $selected = " selected";
    }
    else
    {
        $selected = "";
    }
    $lang_drop_down = $lang_drop_down . "<option value=" . $avail_lang[$i]['value'] . "$selected>" . $avail_lang[$i]['name'] . "</option>\n";
}

global $l_global_mmenu;
$smarty->assign("title", $title);
$smarty->assign("l_global_mmenu", $l_global_mmenu);
$smarty->assign("l_opt_chpass", $l_opt_chpass);
$smarty->assign("l_opt_curpass", $l_opt_curpass);
$smarty->assign("l_opt_newpass", $l_opt_newpass);
$smarty->assign("l_opt_newpagain", $l_opt_newpagain);
$smarty->assign("l_opt_usenew", $l_opt_usenew);
$smarty->assign("l_opt_lang", $l_opt_lang);
$smarty->assign("l_opt_userint", $l_opt_userint);
$smarty->assign("l_opt_select", $l_opt_select);
$smarty->assign("lang_drop_down", $lang_drop_down);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_opt_enabled", $l_opt_enabled);
$smarty->assign("l_opt_save", $l_opt_save);
$smarty->assign("ship_name", $shipinfo['name']);
$smarty->assign("allow_shoutbox", $allow_shoutbox);
$smarty->assign("sb_footer", $playerinfo['sb_footer']);
$smarty->assign("sb_backwards", $playerinfo['sb_backwards']);
$smarty->assign("sb_lines", $playerinfo['sb_lines']);
$smarty->assign("use_gravatar", $playerinfo['use_gravatar']);
$smarty->display("$templateset/options.tpl");

include_once ("./footer.php");
?>
