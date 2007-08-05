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
// File: includes/load_languages.php
// todo: test to see if you can move the global call one line lower

function load_languages($db, $raw_prefix, $file)
{
    if (!is_object($db))
    {
        return FALSE; // Database is not installed.
    }

    // Pull in language strings from the database
    $debug_query = $db->Execute("SELECT name,value FROM {$raw_prefix}languages where category=?", array($file));
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    while ($debug_query && !$debug_query->EOF)
    {
        $row = $debug_query->fields;
        global $$row['name'];
        $$row['name'] = $row['value'];
        $debug_query->MoveNext();
    }

    return TRUE; // Database is installed, languages were pulled.
}
?>
