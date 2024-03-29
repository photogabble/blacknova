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
// File: ship.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_ship_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('ship', 'planet', 'main', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";

if (!isset($ship_id))
{
    $ship_id = null;
}

$res = $db->Execute("SELECT team, ship_name, character_name, sector FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;
$res2 = $db->Execute("SELECT team, ship_name, character_name, sector FROM {$db->prefix}ships WHERE ship_id = ?;", array($ship_id));
Bnt\Db::logDbErrors($db, $res2, __LINE__, __FILE__);
$othership = $res2->fields;

if ($othership['sector'] != $playerinfo['sector'])
{
    echo $langvars['l_ship_the'] . " <font color=white>" . $othership['ship_name'] . "</font> " . $langvars['l_ship_nolonger'] . " " . $playerinfo['sector'] . "<br>";
}
else
{
    $_SESSION['ship_selected'] = $ship_id;
    echo $langvars['l_ship_youc'] . " <font color=white>" . $othership['ship_name'] . "</font>, " . $langvars['l_ship_owned'] . " <font color=white>" . $othership['character_name'] . "</font>.<br><br>";
    echo $langvars['l_ship_perform'] . "<br><br>";
    echo "<a href=scan.php?ship_id=$ship_id>" . $langvars['l_planet_scn_link'] . "</a><br>";

    if (!Bad\Team::sameTeam($playerinfo['team'], $othership['team']))
    {
        echo "<a href=attack.php?ship_id=$ship_id>" . $langvars['l_planet_att_link'] . "</a><br>";
    }

    echo "<a href=mailto.php?to=$ship_id>" . $langvars['l_send_msg'] . "</a><br>";
}

echo "<br>";
Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
