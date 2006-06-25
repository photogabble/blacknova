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
// File: includes/ini_to_mem.php
//
// Function for placing values in memory from the ini file (used prior to db)

function ini_to_mem ($ini_file)
{
    // Store the ini values into memory.

    // This is a loop, that reads a ini file, of the type variable = value.
    // It will loop thru the list of the ini variables, and push them into memory.
    $ini_keys = parse_ini_file($ini_file, true);

    foreach ($ini_keys as $config_category=>$whatever2)
    {
        foreach ($whatever2 as $config_key=>$config_value)
        {
            if ($config_category == 'make_galaxy' || $config_category == 'common' || $config_category == 'install')
            {
                global $$config_key;
                $$config_key = $config_value;
            }
        }
    }

    return TRUE;
}
?>
