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
// File: includes/get_info.php

function get_info($db)
{
    // Dynamic functions
    dynamic_loader ($db, "adminlog.php");

    global $raw_prefix, $playerinfo, $portinfo, $shipinfo, $zoneinfo, $sectorinfo, $classinfo, $igbinfo, $accountinfo; 
    global $templateset;

    if (isset($_SESSION['email']))
    {
        $debug_query = $db->SelectLimit("SELECT * FROM {$raw_prefix}users WHERE email='$_SESSION[email]'",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $accountinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}players WHERE account_id='$accountinfo[account_id]'",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;

        if ($playerinfo['credits'] < 0)
        {
            adminlog($db, "LOG_RAW", "Negative value for player credits - possible cheat from - " . $_SESSION['ip_address']);
        }

        $templateset = $playerinfo['template'];
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ships WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $shipinfo = $debug_query->fields;

        $debug_query = $db->CacheSelectLimit("SELECT * FROM {$db->prefix}ship_types WHERE type_id=$shipinfo[class]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $classinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}universe WHERE sector_id=$shipinfo[sector_id]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $sectorinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ports WHERE sector_id=$shipinfo[sector_id]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $portinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}zones WHERE zone_id=$sectorinfo[zone_id]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $zoneinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=$playerinfo[player_id]",1);
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $igbinfo = $debug_query->fields;

        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
?>
