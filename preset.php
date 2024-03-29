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
// File: preset.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

$langvars = Bnt\Translate::load($pdo_db, $lang, array('presets'));
$title = $langvars['l_pre_title'];
$body_class = 'bnt';
Bnt\Header::display($pdo_db, $lang, $template, $title, $body_class);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('presets', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";
echo "<body class ='" . $body_class . "'>";
$result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

// Pull the presets for the player from the db.
$i=0;
$debug_query = $db->Execute("SELECT * FROM {$db->prefix}presets WHERE ship_id=?", array($playerinfo['ship_id']));
Bnt\Db::logDbErrors($db, $debug_query ,__LINE__, __FILE__);
while (!$debug_query->EOF)
{
    $presetinfo[$i] = $debug_query->fields;
    $debug_query->MoveNext();
    $i++;
}

$preset_list = array();

// Filter the array of presets from the form submission
if (array_key_exists('preset', $_POST))
{
    foreach ($_POST['preset'] as $key => $value)
    {
        // Returns null if it doesn't have it set, boolean false if its set but fails to validate and the actual value if it all passes.
        $preset_list[$key] = filter_var($_POST['preset'][$key], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => $bntreg->sector_max)));
    }
}
$change = filter_input(INPUT_POST, 'change', FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 1)));

foreach ($preset_list as $index => $preset)
{
    if ($preset === false)
    {
        $change = 0;
        $result = str_replace("[preset]", $_POST['preset'][$index], $langvars['l_pre_exceed']);
        $result = str_replace("[sector_max]", $bntreg->sector_max, $result);
        echo $result . "<br>\n";
    }
}
echo "<br>\n";

if ($change !== 1)
{
    echo "<form accept-charset='utf-8' action='preset.php' method='post'>";
    for ($x=0; $x<$bntreg->preset_max; $x++)
    {
        echo "<div style='padding:2px;'>Preset " . ($x + 1) . ": <input type='text' name='preset[$x]' size='6' maxlength='6' value='" . $presetinfo[$x]['preset'] . "'></div>";
    }

    echo "<input type='hidden' name='change' value='1'>";
    echo "<div style='padding:2px;'><input type='submit' value=" . $langvars['l_pre_save'] . "></div>";
    echo "</form>";
    echo "<br>\n";
}
else
{
    foreach ($_POST['preset'] as $key=>$value)
    {
        if ($key < $bntreg->preset_max)
        {
            $update = $db->Execute("UPDATE {$db->prefix}presets SET preset = ? WHERE preset_id = ?;", array($preset_list[$key], $presetinfo[$key]['preset_id']));
            Bnt\Db::logDbErrors($db, $update, __LINE__, __FILE__);
            $preset_result_echo = str_replace("[preset]", "<a href=rsmove.php?engage=1&destination=$preset_list[$key]>$preset_list[$key]</a>", $langvars['l_pre_set_loop']);
            $preset_result_echo = str_replace("[num]", $key + 1, $preset_result_echo);
            echo $preset_result_echo . "<br>";
        }
    }
}

Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
