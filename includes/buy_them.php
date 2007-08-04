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
// File: buy_them.php
function buy_them($db,$player_id, $how_many = 1)
{
    global $shipinfo;

    for ($i=1; $i<=$how_many; $i++)
    {
        $debug_query = $db->Execute("INSERT INTO {$db->prefix}spies (spy_id, active, owner_id, planet_id, ship_id, job_id, spy_percent, move_type) " .
                                    "values (?,?,?,?,?,?,?,?)", array('','N',$player_id,'0',$shipinfo['ship_id'],'0','0.0','toship'));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }  
}
?>
