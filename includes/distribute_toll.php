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
// File: inclues/distribute_toll.php

function distribute_toll($db, $destination, $toll, $total_fighters)
{
    dynamic_loader ($db, "playerlog.php");

    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? AND defense_type ='F'", array($destination));
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            $row = $result3->fields;
            $toll_amount = ROUND(($row['quantity'] / $total_fighters) * $toll);
            $debug_query = $db->Execute("UPDATE {$db->prefix}players set credits=credits+? WHERE player_id=?", array($toll_amount, $row['player_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            playerlog($db,$row[player_id], "LOG_TOLL_RECV", "$toll_amount|$destination");
            $result3->MoveNext();
        }
    }
}
?>
