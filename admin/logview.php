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
// File: admin/logview.php

$pos = (strpos($_SERVER['PHP_SELF'], "/logview.php"));
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

// Include the sha256 backend
include_once ("./backends/sha256/shaclass.php");

echo "<form action=log.php method=post>" .
     "<input type=hidden name=sha256swordfish value=".sha256::hash($adminpass).">" .
     "<input type=hidden name=player value=0>" .
     "<input type=submit value=\"View admin log\">" .
     "</form>" .
     "<form action=log.php method=post>" .
     "<input type=hidden name=sha256swordfish value=".sha256::hash($adminpass).">" .
     "<select name=player>";

$res = $db->execute("select player_id, character_name FROM {$db->prefix}players ORDER BY character_name ASC");
while (!$res->EOF)
{
    $player = $res->fields;
    echo "<option value=$player[player_id]>$player[character_name]</option>";
    $res->MoveNext();
}



echo "</select>&nbsp;&nbsp;" .
     "<input type=submit value=\"View player log\">" .
     "</form><hr size=\"1\" width=\"80%\">";
?>
