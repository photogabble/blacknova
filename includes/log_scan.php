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
// File: includes/log_scan.php
// todo: update record if one exists, instead of returning false.

function log_scan($db, $player_id,$sector_id)
{
    // Check if the player has already scanned that sector - no need to double the
    // db record
    $debug_query = $db->Execute("SELECT * FROM {$db->prefix}scan_log WHERE player_id=? and sector_id=?", array($player_id, $sector_id));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if ($debug_query->EOF)
    {
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}scan_log (player_id,sector_id) VALUES " .
                                    "(?,?)", array($player_id, $sector_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
?>
