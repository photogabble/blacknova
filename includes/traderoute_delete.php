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
// File: includes/traderoute_delete.php

function traderoute_delete()
{
    // Dynamic functions
    dynamic_loader ($db, "traderoute_die.php");
    global $playerinfo;
    global $confirm;
    global $num_traderoutes;
    global $traderoute_id;
    global $traderoutes;
    global $l_tdr_returnmenu, $l_tdr_tdrdoesntexist, $l_tdr_notowntdr, $l_tdr_tdrdeleted;
    global $db;

    $query = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=?", array($_GET['traderoute_id']));
    if (!$query || $query->EOF)
    {
        traderoute_die($l_tdr_tdrdoesntexist);
    }

    $delroute = $query->fields;

    if ($delroute['owner'] != $playerinfo['player_id'])
    {
        traderoute_die($l_tdr_notowntdr);
    }

    if (empty($_GET['confirm']))
    {
        $num_traderoutes = 1;
        $traderoutes[0] = $delroute;
        // here it continues to the main file area to print the route
    }
    else
    {
        $query = $db->Execute("DELETE FROM {$db->prefix}traderoutes WHERE traderoute_id=?", $_GET['traderoute_id']));
        echo "$l_tdr_tdrdeleted <a href=traderoute.php>$l_tdr_returnmenu</a>";
        traderoute_die("");
    }
}
?>
