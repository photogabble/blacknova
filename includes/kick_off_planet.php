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
// File: inclues/kick_off_planet.php

function kick_off_planet($db, $player_id,$whichteam)
{
    dynamic_loader ($db, "playerlog.php");

    $result1 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner=?", array($player_id));
    db_op_result($db,$result1,__LINE__,__FILE__);

    if ($result1 > 0)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE on_planet = 'Y' and planet_id=? and player_id !=?", array($row['planet_id'], $player_id));
            db_op_result($db,$result2,__LINE__,__FILE__);
            if ($result2 > 0)
            {
                while (!$result2->EOF )
                {
                    $cur = $result2->fields;
                    $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET on_planet = 'N',planet_id = '0' WHERE ship_id=?", array($cur['ship_id']));
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    playerlog($db,$cur[player_id], "LOG_PLANET_EJECT", "$cur[sector]|$row[character_name]");
                    $result2->MoveNext();
                }
            }
            $result1->MoveNext();
        }
    }
}
?>
