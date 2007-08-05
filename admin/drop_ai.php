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
// File: admin/drop_ai.php

$pos = (strpos($_SERVER['PHP_SELF'], "/drop_ai.php"));
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

echo "<h1>Drop and Re-Install " . $ai_name . " Database</h1>";
echo "<h3>This will DELETE All " . $ai_name . " records from the <i>ships</i> TABLE then DROP and reset the <i>" . $ai_name . "</i> TABLE</h3>";
echo "<form action=admin.php method=post>";
if (empty($operation))
{
    echo "<br>";
    echo "<h2><font color=red>Are You Sure?</font></h2><br>";
    echo "<input type=hidden name=operation value=drop_ai>";
    echo "<input type=hidden name=menu value=ai>";
    echo "<input type=submit value=Drop>";
}
elseif ($operation == "drop_ai")
{
    // Delete all AI in the ships table
    echo "Deleting AI records in the ships table...<br>";
    $res = $db->Execute("SELECT ship_id FROM {$db_prefix}players LEFT JOIN {$db_prefix}ships ON {$db_prefix}players.player_id = {$db_prefix}ships.player_id WHERE email LIKE '%@aiplayer'");
    while (!$res->EOF)
    {
        $ship_id = $res->fields['ship_id'];
        $debug_query = $db->Execute("DELETE FROM {$db_prefix}ships WHERE ship_id=?", array($ship_id));
        db_op_result($debug_query,__LINE__,__FILE__);
        $res->MoveNext();
    }

    $debug_query = $db->Execute("DELETE FROM {$db_prefix}players WHERE email LIKE '%@aiplayer'");
    db_op_result($debug_query,__LINE__,__FILE__);
    echo "deleted.<br>";

    // Drop AI table
    echo "Dropping " . $ai_name . " table...<br>";
    $debug_query = $db->Execute("DROP TABLE IF EXISTS {$db_prefix}ai");
    db_op_result($debug_query,__LINE__,__FILE__);
    echo "dropped.<br>";

    // Create AI table
    echo "Re-Creating table: ai...<br>";
    $debug_query = $db->Execute("CREATE TABLE {$db_prefix}ai (" .
                 "ai_id char(40) NOT NULL," .
                 "active char(1) DEFAULT 'Y' NOT NULL," .
                 "aggression smallint(5) DEFAULT '0' NOT NULL," .
                 "orders smallint(5) DEFAULT '0' NOT NULL," .
                 "PRIMARY KEY (ai_id)," .
                 "KEY ai_id (ai_id)" .
                 ")");
    db_op_result($debug_query,__LINE__,__FILE__);
    echo "created.<br>";
}
else
{
    echo "Invalid operation";
}

echo "<input type=hidden name=module value=drop_ai>";
echo "<input type=hidden name=menu value=ai>";
echo "</form>";

?>
