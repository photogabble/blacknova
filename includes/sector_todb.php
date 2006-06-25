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
// File: includes/sector_todb.php
// todo: deal with failure condition in initial insert generation. see comments.

// Function for dumping sectors to the db.
function sector_todb($db, $array, $method, $sector_id)
{
    // Execute a query to get an empty recordset
    $debug_query_rs = $db->SelectLimit("SELECT * FROM {$db->prefix}universe WHERE sector_id=?",1,-1, array($sector_id));
    db_op_result($db,$debug_query_rs,__LINE__,__FILE__);

    $tablename = $db->prefix . "universe";

    // Adodb generates an insert statement for pushing the array into the db.
    $debug_query_insert  = $db->GetInsertSQL($tablename, $array);
    db_op_result($db,$debug_query_insert,__LINE__,__FILE__); // handle failures?

    // Now execute the generated query for insert/update
    $debug_query = $db->Execute($debug_query_insert);
    return db_op_result($db,$debug_query,__LINE__,__FILE__);
}
?>
