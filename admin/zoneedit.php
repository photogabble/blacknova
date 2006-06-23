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
// File: zoneedit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/zoneedit.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

echo "<b>Zone editor</b>";
echo "<br>";
echo "<form action=admin.php method=post>";
if (empty($_POST['zone']))
{
    echo "<select size=20 name=zone>";
    $res = $db->Execute("select zone_id,zone_name FROM {$db->prefix}zones ORDER BY zone_name");
    while (!$res->EOF)
    {
        $row = $res->fields;
        echo "<option value=$row[zone_id]>$row[zone_name]</option>";
        $res->MoveNext();
    }

    echo "</select>";
    echo "<input type=hidden name=operation value=editzone>";
    echo "&nbsp;<input type=submit value=Edit>";
}
else
{
    $zone = $_POST['zone'];
    if ($_POST['operation'] == "editzone")
    {
        $res = $db->Execute("select * FROM {$db->prefix}zones WHERE zone_id=?", array($zone));
        $row = $res->fields;
        echo "<table border=0 cellspacing=0 cellpadding=5>";
        echo "<tr><td>Zone ID</td><td>$row[zone_id]</td></tr>";
        echo "<tr><td>Zone name</td><td><input type=text name=zone_name value=\"$row[zone_name]\"></td></tr>";
        echo "<tr><td>Allow Attack</td><td><input type=checkbox name=zone_attack value=ON " . checked($row['allow_attack']) . "></td>";
        echo "<tr><td>Allow WarpEdit</td><td><input type=checkbox name=zone_warpedit value=ON " . checked($row['allow_warpedit']) . "></td>";
        echo "<tr><td>Allow Planet</td><td><input type=checkbox name=zone_planet value=ON " . checked($row['allow_planet']) . "></td>";
        echo "</table>";
        echo "<tr><td>Max Avg combat level allowed</td><td><input type=text name=zone_level value=\"$row[max_level]\"></td></tr>";
        echo "<br>";
        echo "<input type=hidden name=zone value=$zone>";
        echo "<input type=hidden name=operation value=savezone>";
        echo "<input type=submit value=save>";
    }
    elseif ($_POST['operation'] == "savezone")
    {
        // update database
        $_zone_attack = empty($zone_attack) ? "N" : "Y";
        $_zone_warpedit = empty($zone_warpedit) ? "N" : "Y";
        $_zone_planet = empty($zone_planet) ? "N" : "Y";
        $debug_query = $db->Execute("UPDATE {$db->prefix}zones SET zone_name=?, " .
                                    "allow_attack=?, allow_warpedit=?, " .
                                    "allow_planet=?, max_level=? WHERE zone_id=?", array($zone_name, $_zone_attack, $_zone_warpedit, $_zone_planet, $zone_level, $zone));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo "<input type=submit value=\"Return to Zone Editor \">";
    }
    else
    {
        echo "Invalid operation";
    }
}

echo "<input type=hidden name=menu value=zoneedit>";
echo "</form>";
?>
