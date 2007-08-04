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
// File: includes/transfer_to_ship.php
// todo: move globals to function call, fix active=n (see comments)

function transfer_to_ship($db,$player_id, $planet_id, $how_many = 1)
{
    global $shipinfo;

    $res = $db->SelectLimit("SELECT spy_id FROM {$db->prefix}spies WHERE " .
                            "owner_id = $player_id AND planet_id = $planet_id",
                            $how_many);// AND active = 'N'
                            // The and active = n got dropped?
    $how_many2 = $res->RecordCount();
  
    if (!$how_many2)
    {
        return FALSE; // Was 0.
    }
    else  
    {
        while (!$res->EOF)
        {
            $spy = $res->fields['spy_id'];
            $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET planet_id = 0, ship_id = $shipinfo[ship_id], active = 'N', job_id = '0', spy_percent = '0.0' WHERE spy_id = $spy");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $res->MoveNext();
        }
        return $how_many2;
    }   
}
?>
