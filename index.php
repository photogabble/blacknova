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
// File: index.php

$title = '';
$no_body = 1;
include_once ("./global_includes.php");

// Load language variables
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'login');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'new');
load_languages($db, $raw_prefix, 'news');
load_languages($db, $raw_prefix, 'new2');
load_languages($db, $raw_prefix, 'main');
load_languages($db, $raw_prefix, 'option');
$db_installed = load_languages($db, $raw_prefix, 'index');

if (!$db_installed)
{
    if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
    {
        $add_slash_to_url = '/';
    }

    // Much smoother - no broken header/footer issues, and seamless for user.
//    header("Location: http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "install.php");
    exit();
}

$style_sheet_file = "templates/$templateset/styles/index-style.css";
include_once ("./header.php");

$login_drop_down = '';

$maxval = count($avail_lang);
for ($i=0; $i<$maxval; $i++)
{
    if ($avail_lang[$i]['value'] == $langdir)
    {
        $selected = " selected=\"selected\"";
    }
    else
    {
        $selected = "";
    }

    $login_drop_down = $login_drop_down . "<option value=" . $avail_lang[$i]['value'] . "$selected>" . $avail_lang[$i]['name'] . "</option>\n";
}

$debug_query = $db->Execute("SELECT count(gamenumber) as count FROM {$raw_prefix}instances");
db_op_result($db,$debug_query,__LINE__,__FILE__);
if ($debug_query && !$debug_query->EOF)
{
    $num_of_games = $debug_query->fields['count'];
}
else
{
    $num_of_games = 0;
}

$game_drop_down='';
for ($i=1; $i<=$num_of_games; $i++)
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

$template->assign("l_news_title", $l_news_title);
$template->assign("l_in_contact", $l_in_contact);
$template->assign("l_in_main_forums", $l_in_main_forums);
$template->assign("l_in_dev_forums", $l_in_dev_forums);
$template->assign("l_in_your_email", $l_in_your_email);
$template->assign("l_in_guide", $l_in_guide);
$template->assign("l_in_gm_active", $l_in_gm_active);
$template->assign("l_in_stats", $l_in_stats);
$template->assign("l_help", $l_help);
$template->assign("l_in_gm", $l_in_gm);
$template->assign("l_in_lang", $l_in_lang);
$template->assign("l_in_join", $l_in_join);
$template->assign("l_in_curgame_info", $l_in_curgame_info);
$template->assign("l_password", $l_password);
$template->assign("l_email", $l_email);
$template->assign("l_rankings", $l_rankings);
$template->assign("l_in_gamenum", $l_in_gamenum);
$template->assign("l_in_signup", $l_in_signup);
$template->assign("l_in_newusr", $l_in_newusr);
$template->assign("l_new_forgotpw", $l_new_forgotpw);
$template->assign("l_in_devgm", $l_in_devgm);
$template->assign("l_in_2ndgm", $l_in_2ndgm);
$template->assign("l_in_maingm", $l_in_maingm);
$template->assign("l_in_isdevd", $l_in_isdevd);
$template->assign("l_in_whatis", $l_in_whatis);
$template->assign("l_yes", $l_yes);
$template->assign("l_project_name", $l_project_name);
$template->assign("l_in_bnt_is", $l_in_bnt_is);
$template->assign("l_in_canrun", $l_in_canrun);
$template->assign("l_in_others", $l_in_others);
$template->assign("l_in_nopass", $l_in_nopass);
$template->assign("l_in_multigames", $l_in_multigames);
$template->assign("l_in_new2day", $l_in_new2day);
$template->assign("l_in_bnt_is", $l_in_bnt_is);
$template->assign("num_of_games", $num_of_games);
$template->assign("l_return_to_site", $l_return_to_site);
$template->assign("l_opt_lang", $l_opt_lang);
$template->assign("l_newplayer", $l_newplayer);
$template->assign("l_playername", $l_playername);
$template->assign("l_settings", $l_settings);
$template->assign("l_login_pw", $l_login_pw);
$template->assign("game_drop_down",$game_drop_down);
$template->assign("login_drop_down",$login_drop_down);
$template->assign("l_new_pname", $l_new_pname);
$template->assign("l_login_pw", $l_login_pw);
$template->assign("l_login_forgot_pw", $l_login_forgot_pw);
$template->assign("l_login_chooseres", $l_login_chooseres);
$template->assign("l_login_emailus", $l_login_emailus);
$template->assign("admin_mail", $admin_mail);
$template->assign("site_name", $site_name);
$template->assign("l_login_prbs", $l_login_prbs);
$template->assign("l_login_title", $l_login_title);
$template->assign("l_faq", $l_faq);
$template->assign("l_forums", $l_forums);
$template->assign("l_rankings", $l_rankings);
$template->assign("l_login_settings", $l_login_settings);
$template->assign("avail_lang", $avail_lang);
$template->assign("login_language_change", $l_login_change);
$template->assign("templateset", $templateset);
$template->display("$templateset/index.tpl");
include_once ("./footer.php");
?>
