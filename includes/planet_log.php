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
// File: includes/planet_log.php

function planet_log($db, $planet, $owner, $player_id, $action)
{
    $stamp = date("Y-m-d H:i:s");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}planet_log (planet_id, player_id, owner_id, action, time) VALUES " .
                                "(?,?,?,?,?)", array($planet, $player_id, $owner, $action, $stamp));
    db_op_result($db, $debug_query, __LINE__, __FILE__);
}
?>
