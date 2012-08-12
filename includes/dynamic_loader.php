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
// File: includes/dynamic_loader.php

function dynamic_loader ($db, $mod)
{
    if (is_object($db)) // If database is available, check the db for a mod.
    {
        // Check database for applicable mods
        $debug_query = $db->Execute("SELECT * FROM {$db->prefix}mods where file=?", array($mod));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if (!$debug_query || $debug_query->EOF)
        {
            $mod_file = '';
        }
        else
        {
            $mod_file = 'mods/' . $debug_query->fields['file'];
        }
    }
    else // If database isn't available yet, just load the include.
    {
        $mod_file = '';
    }

    // Check mod exists at the location the database says it is at
    if (is_file($mod_file) && $mod_file != '')
    {
        // todo: Parse check for mod
        $return = include_once($mod_file);
    }
    else // load original function, mod is unavailable for some reason.
    {
        $return = include_once 'includes/'. $mod;
    }

    return $return;
}
?>
