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
// File: feedback.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('feedback', 'galaxy', 'common', 'global_includes', 'global_funcs', 'footer'));

$title = $langvars['l_feedback_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

echo "<h1>" . $title . "</h1>\n";

$result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

// Detect if this variable exists, and filter it. Returns false if anything wasn't right.
$content = null;
$content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING); // URL doesn't allow spaces, string does.
if (mb_strlen(trim($content)) === 0)
{
    $content = false;
}

if ($content === false || $content === null)
{
    echo "<form accept-charset='utf-8' action=feedback.php method=post>\n";
    echo "<table>\n";
    echo "<tr><td>" . $langvars['l_feedback_to'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_from'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_topi'] . "</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=" . $langvars['l_feedback_feedback'] . "></td></tr>\n";
    echo "<tr><td>" . $langvars['l_feedback_message'] . "</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>\n";
    echo "<tr><td></td><td><input type=submit value=" . $langvars['l_submit'] . "><input type=reset value=" . $langvars['l_reset'] . "></td>\n";
    echo "</table>\n";
    echo "</form>\n";
    echo "<br>" . $langvars['l_feedback_info'] . "<br>\n";
}
else
{
    $link_to_game = "http://" . $_SERVER['HTTP_HOST'] . Bnt\SetPaths::setGamepath();
    mail("$bntreg->admin_mail", $langvars['l_feedback_subj'], "IP address - " . $_SERVER['REMOTE_ADDR'] . "\r\nGame Name - {$playerinfo['character_name']}\r\nServer URL - {$link_to_game}\r\n\r\n{$_POST['content']}", "From: {$playerinfo['email']}\r\nX-Mailer: PHP/" . phpversion());
    echo $langvars['l_feedback_messent'] . "<br><br>";
}

echo "<br>\n";
if (empty ($_SESSION['username']))
{
    echo str_replace("[here]", "<a href='index.php'>" . $langvars['l_here'] . "</a>", $langvars['l_global_mlogin']);
}
else
{
    Bnt\Text::gotoMain($db, $lang, $langvars);
}

Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
