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

include_once './global_includes.php';

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
include_once './header.php';

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
$template->assign("title", $title);
$template->assign("l_global_mmenu", $l_global_mmenu);
$template->assign("l_opt_chpass", $l_opt_chpass);
$template->assign("l_opt_curpass", $l_opt_curpass);
$template->assign("l_opt_newpass", $l_opt_newpass);
$template->assign("l_opt_newpagain", $l_opt_newpagain);
$template->assign("l_opt_usenew", $l_opt_usenew);
$template->assign("l_opt_lang", $l_opt_lang);
$template->assign("l_opt_userint", $l_opt_userint);
$template->assign("l_opt_select", $l_opt_select);
$template->assign("lang_drop_down", $lang_drop_down);
$template->assign("color_header", $color_header);
$template->assign("color_line1", $color_line1);
$template->assign("color_line2", $color_line2);
$template->assign("l_opt_enabled", $l_opt_enabled);
$template->assign("l_opt_save", $l_opt_save);
$template->assign("ship_name", $shipinfo['name']);
$template->assign("allow_shoutbox", $allow_shoutbox);
$template->assign("sb_footer", $playerinfo['sb_footer']);
$template->assign("sb_backwards", $playerinfo['sb_backwards']);
$template->assign("sb_lines", $playerinfo['sb_lines']);
$template->assign("use_gravatar", $playerinfo['use_gravatar']);
$template->display("$templateset/options.tpl");

include_once './footer.php';
?>
