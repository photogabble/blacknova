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
// File: inclues/defense_vs_defense.php

function defense_vs_defense($db, $player_id)
{
    dynamic_loader ($db, "playerlog.php");

    $result1 = $db->Execute("SELECT * FROM {$db->prefix}sector_defense WHERE player_id=?", array($player_id));
    db_op_result($db,$result1,__LINE__,__FILE__);

    if ($result1 > 0)
    {
        while (!$result1->EOF)
        {
            $row = $result1->fields;
            $deftype = $row['defense_type'] == 'F' ? 'Fighters' : 'Mines';
            $qty = $row['quantity'];
            $result2 = $db->Execute("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? and player_id!=? ORDER BY quantity DESC", array($row['sector_id'], $player_id));
            db_op_result($db,$result2,__LINE__,__FILE__);
            if ($result2 > 0)
            {
                while (!$result2->EOF && $qty > 0)
                {
                    $cur = $result2->fields;
                    $targetdeftype = $cur[defense_type] == 'F' ? $l_fighters : $l_mines;
                    if ($qty > $cur['quantity'])
                    {
                        $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE defense_id=?", array($cur['defense_id']));
                        $qty -= $cur['quantity'];
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense SET quantity=? WHERE defense_id=?", array($qty, $row['defense_id']));
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        playerlog($db,$cur[player_id], "LOG_DEFS_DESTROYED", "$cur[quantity]|$targetdeftype|$row[sector_id]");
                        playerlog($db,$row[player_id], "LOG_DEFS_DESTROYED", "$cur[quantity]|$deftype|$row[sector_id]");
                    }
                    else
                    {
                        $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE defense_id=?", array($row['defense_id']));
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense SET quantity=quantity-? WHERE defense_id=?", array($qty, $cur['defense_id']));
                        db_op_result($db,$debug_query,__LINE__,__FILE__);

                        playerlog($db,$cur[player_id], "LOG_DEFS_DESTROYED", "$qty|$targetdeftype|$row[sector_id]");
                        playerlog($db,$row[player_id], "LOG_DEFS_DESTROYED", "$qty|$deftype|$row[sector_id]");
                        $qty = 0;
                    }
                    $result2->MoveNext();
                }
            }
            $result1->MoveNext();
        }
        $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE quantity <= 0");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }
}
?>
