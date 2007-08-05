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
// File: includes/adminlog.php

function adminlog($db, $log_type, $data = '')
{
/*
    $debug_query = $db->Execute("SHOW TABLES LIKE '{$db->prefix}logs'");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $row = $debug_query->fields;
    if ($row !== false)
*/
    $throw_away = $db->SelectLimit("SELECT * FROM {$db->prefix}logs",1);
    if ($throw_away)
    {
        // write log_entry to the admin log
        if (!empty($log_type))
        {
            $stamp = date("Y-m-d H:i:s");
            $debug_query = $db->Execute("INSERT INTO {$db->prefix}logs (player_id, type, log_time, log_data) " .
                                        "VALUES (?,?,?,?)", array (0, $log_type, $stamp, $data));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
}
?>
