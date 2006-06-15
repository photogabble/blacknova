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
// File: includes/db_op_result.php
// todo: move global to function call

function db_op_result($db, $query, $served_line, $served_page)
{
    // Dynamic functions
//    dynamic_loader ($db, "adminlog.php");
    global $cumulative;

    if ($db->ErrorMsg() == '')
    {
        return true;
    }
    else
    {
        $dberror = "A Database error occurred in " . $served_page .
                   " on line " . ($served_line-1) . 
                   " (called from: $_SERVER[PHP_SELF]): " . $db->ErrorMsg();
        $dberror = str_replace("'","&#39;",$dberror); // Allows the use of apostrophes.
        return $db->ErrorMsg();
        adminlog($db, "LOG_RAW", $dberror);
        $cumulative = 1; // For areas with multiple actions needing status - 0 is all
                         // good so far, 1 is at least one bad.
    }
}
?>
