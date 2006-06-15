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
// File: fix_magic_quotes.php
//
// Function originally provided at http://www.nyphp.org/phundamentals/storingretrieving.php
function fix_magic_quotes ($var = NULL, $sybase = NULL)
{
    // if sybase style quoting isn't specified, use ini setting
    if (!isset ($sybase))
    {
        $sybase = ini_get ('magic_quotes_sybase');
    }

    // if no var is specified, fix all affected superglobals
    if (!isset ($var))
    {
        // if magic quotes is enabled
        if (get_magic_quotes_gpc())
        {
            // workaround because magic_quotes does not change $_SERVER['argv']
            $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : NULL; 

            // fix all affected arrays
            foreach(array('_ENV', '_REQUEST', '_GET', '_POST', '_COOKIE', '_SERVER') as $var)
            {
                $GLOBALS[$var] = fix_magic_quotes ($GLOBALS[$var], $sybase);
            }

            $_SERVER['argv'] = $argv;

            // turn off magic quotes, this is so scripts which
            // are sensitive to the setting will work correctly
            ini_set ('magic_quotes_gpc', 0);
        }

        // disable magic_quotes_sybase
        if ($sybase)
        {
            ini_set ('magic_quotes_sybase', 0);
        }

        // disable magic_quotes_runtime
        set_magic_quotes_runtime (0);
        return TRUE;
    }

    // if var is an array, fix each element
    if (is_array ($var))
    {
        foreach ( $var as $key => $val )
        {
            $var[$key] = fix_magic_quotes ($val, $sybase);
        }

        return $var;
    }

    // if var is a string, strip slashes
    if ( is_string ($var) )
    {
        return $sybase ? str_replace ('\'\'', '\'', $var) : stripslashes ($var);
    }

    // otherwise ignore
    return $var;
}
?>
