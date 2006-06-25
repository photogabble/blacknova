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
// File: inclues/explode_mines.php

function explode_mines($db,$sector, $num_mines)
{
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id=? and defense_type ='M' order by quantity ASC", array($sector));
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF && $num_mines > 0)
        {
            $row = $result3->fields;
            if ($row['quantity'] > $num_mines)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}sector_defense set quantity=quantity-? WHERE defense_id =?", array($num_mines, $row['defense_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_mines = 0;
            }
            else
            {
                $debug_query = $db->Execute("DELETE FROM {$db->prefix}sector_defense WHERE defense_id=?", array($row['defense_id']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                $num_mines -= $row['quantity'];
            }

            $result3->MoveNext();
        }
    }
}
?>
