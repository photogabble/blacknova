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
// File: options.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$body_class = 'options';

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('options', 'common', 'global_includes', 'global_funcs', 'footer'));
$title = $langvars['l_opt_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title, $body_class);
$players_gateway = new \Bnt\Players\PlayersGateway($pdo_db); // Build a player gateway object to handle the SQL calls
$playerinfo = $players_gateway->selectPlayerInfo($_SESSION['username']);

echo "<body class='options'>\n";
echo "<h1>" . $title . "</h1>\n";
echo "<form accept-charset='utf-8' action=option2.php method=post>\n";
echo "<table>\n";
echo "<tr>\n";
echo "<th colspan=2><strong>" . $langvars['l_opt_chpass'] . "</strong></th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>" . $langvars['l_opt_curpass'] . "</td>\n";
echo "<td><input type=password name=oldpass size=20 maxlength=20 value=\"\"></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>" . $langvars['l_opt_newpass'] . "</td>\n";
echo "<td><input type=password name=newpass1 size=20 maxlength=20 value=\"\"></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>" . $langvars['l_opt_newpagain'] . "</td>\n";
echo "<td><input type=password name=newpass2 size=20 maxlength=20 value=\"\"></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<th colspan=2><strong>" . $langvars['l_opt_lang'] . "</strong></th>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td>" . $langvars['l_opt_select'] . "</td><td><select name=newlang>\n";

$avail_langs = Bnt\Languages::listAvailable($pdo_db, $lang);
foreach($avail_langs as $language_list_item_name => $language_list_item)
{
    if ($language_list_item_name == $playerinfo['lang'])
    {
        $selected = " selected";
    }
    else
    {
        $selected = null;
    }

    echo "<option value='" . $language_list_item_name . "'" . $selected . ">" . $language_list_item['lang_name'] . "</option>\n";
}

echo "</select></td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<br>\n";
echo "<input type=submit value=" . $langvars['l_opt_save'] . ">\n";
echo "</form><br>\n";

Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
