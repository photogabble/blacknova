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
// File: log_move.php
// Todo: Figure out why we aren't doing an update here instead of delete/insert.
// Todo: Possibly to eliminate all previous rows, and not just one previous?
// Todo: Also, recode to support a return that shows success/failure.

function log_move($db, $player_id, $ship_id, $source, $destination, $class, $error)
{
    $debug_query = $db->Execute("DELETE FROM {$db->prefix}movement_log WHERE player_id = $player_id and source = $source and destination = $destination");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $stamp = date("Y-m-d H:i:s");
    $debug_query = $db->Execute("INSERT INTO {$db->prefix}movement_log (player_id, ship_id, source, time, destination, ship_class, error_factor) " .
                                "VALUES (?,?,?,?,?,?,?)", array ($player_id, $ship_id, $source, $stamp, $destination, $class, $error));
    db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
