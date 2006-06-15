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
// File: clearklog.php

$pos = (strpos($_SERVER['PHP_SELF'], "/clearklog.php"));
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

echo "<h1>Clear All " . $ai_name . "Logs</h1>";
echo "<h3>This will DELETE all " . $ai_name . "log files</h3>";
echo "<form action=\"admin.php\" method=\"post\">";
if (empty($operation))
{
    echo "<br>";
    echo "<h2><font color=\"red\">Are You Sure?</font></h2><br>";
    echo "<input type=\"hidden\" name=\"operation\" value=\"clear_ai_log\">";
    echo "<input type=\"hidden\" name=\"menu\" value=\"ai\">";
    echo "<input type=\"submit\" value=\"Clear\">";
}
elseif ($operation == "clear_ai_log")
{
    $res = $db->Execute("SELECT email,player_id FROM {$db_prefix}players WHERE email LIKE '%@aiplayer'");
    while (!$res->EOF)
    {
        $row = $res->fields;
        $debug_query = $db->Execute("DELETE FROM {$db_prefix}logs WHERE player_id=$row[player_id]");
        db_op_result($debug_query,__LINE__,__FILE__);
        echo "Log for player_id $row[player_id] cleared.<br>";
        $res->MoveNext();
    }
}
else
{
    echo "Invalid operation";
}

echo "<input type=\"hidden\" name=\"module\" value=\"clearlog\">";
echo "</form>";

?>
