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
// File: source_list.php
function source_list($destination, $debug_query, $db, $db->prefix)
{
    $temp = array();
    $i = 0;
    $debug_query = $db->Execute("SELECT link_start FROM {$db->prefix}links WHERE link_dest=$destination order by link_start");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    while (!$debug_query->EOF)
    {
        $res = $debug_query->fields;
        $temp[$i] = $res['link_start'];
        $debug_query->MoveNext();
        $i++;
    }
    return $temp;
}
?>
