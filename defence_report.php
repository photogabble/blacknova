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
// File: defence_report.php

require_once './common.php';

Bnt\Login::checkLogin($pdo_db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('defence_report', 'planet_report', 'main', 'device', 'port', 'modify_defences', 'common', 'global_includes', 'global_funcs', 'combat', 'footer', 'news', 'regional'));
$title = $langvars['l_sdf_title'];
Bnt\Header::display($pdo_db, $lang, $template, $title);

echo "<h1>" . $title . "</h1>\n";

$res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array($_SESSION['username']));
Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
$playerinfo = $res->fields;

$query = "SELECT * FROM {$db->prefix}sector_defence WHERE ship_id = ?";
if (!empty($sort))
{
    $query .= " ORDER BY";
    if ($sort == "quantity")
    {
        $query .= " quantity ASC";
    }
    elseif ($sort == "mode")
    {
        $query .= " fm_setting ASC";
    }
    elseif ($sort == "type")
    {
        $query .= " defence_type ASC";
    }
    else
    {
        $query .= " sector_id ASC";
    }
}

$res = $db->Execute($query, array($playerinfo['ship_id']));
Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

$i = 0;
if ($res)
{
    while (!$res->EOF)
    {
        $sector[$i] = $res->fields;
        $i++;
        $res->MoveNext();
    }
}

$num_sectors = $i;
if ($num_sectors < 1)
{
    echo "<br>" . $langvars['l_sdf_none'];
}
else
{
    echo $langvars['l_pr_clicktosort'] . "<br><br>";
    echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
    echo "<tr bgcolor=\"$color_header\">";
    echo "<td><strong><a href=defence_report.php?sort=sector>" . $langvars['l_sector'] . "</a></strong></td>";
    echo "<td><strong><a href=defence_report.php?sort=quantity>" . $langvars['l_qty'] . "</a></strong></td>";
    echo "<td><strong><a href=defence_report.php?sort=type>" . $langvars['l_sdf_type'] . "</a></strong></td>";
    echo "<td><strong><a href=defence_report.php?sort=mode>" . $langvars['l_sdf_mode'] . "</a></strong></td>";
    echo "</tr>";
    $color = $color_line1;
    for ($i = 0; $i < $num_sectors; $i++)
    {
        echo "<tr bgcolor=\"$color\">";
        echo "<td><a href=rsmove.php?engage=1&destination=". $sector[$i]['sector_id'] . ">". $sector[$i]['sector_id'] ."</a></td>";
        echo "<td>" . number_format($sector[$i]['quantity'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . "</td>";
        $defence_type = $sector[$i]['defence_type'] == 'F' ? $langvars['l_fighters'] : $langvars['l_mines'];
        echo "<td> $defence_type </td>";
        $mode = $sector[$i]['defence_type'] == 'F' ? $sector[$i]['fm_setting'] : $langvars['l_n_a'];
        if ($mode == 'attack')
        {
            $mode = $langvars['l_md_attack'];
        }
        else
        {
            $mode = $langvars['l_md_toll'];
        }

        echo "<td> " . $mode . " </td>";
        echo "</tr>";

        if ($color == $color_line1)
        {
            $color = $color_line2;
        }
        else
        {
            $color = $color_line1;
        }
    }
    echo "</table>";
}

echo "<br><br>";
Bnt\Text::gotoMain($db, $lang, $langvars);
Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
