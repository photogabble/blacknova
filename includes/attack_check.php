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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, U$
//
// File: includes/attack_check.php

function attack_check($db, $ip_address, $attack_repeats)
{
    $now = date("Y-m-d");
    $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}logs WHERE log_data " .
                                    "like '%bad login%' and log_time like '%". 
                                    $now . "%' and log_data like '%" . $ip_address . 
                                    "%'", $attack_repeats+1);
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    $count = $debug_query->Recordcount();
    if ($count > $attack_repeats)
    {
        $debug_query = $db->Execute("INSERT INTO {$raw_prefix}ip_bans " .
                                    "(ban_id, ban_mask) values(?,?)", array('',$ip_address));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    if ($count > $attack_repeats)
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
?>
