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
// File: includes/set_langdir.php

function set_langdir($db, $badfile)
{
    global $default_lang, $avail_lang, $raw_prefix;

    if (isset($_POST['newlang']))
    {
        $_SESSION['langdir'] = $_POST['newlang'];
    }

    $default_lang = 'english';
    if (!$badfile)
    {
        $debug_query = $db->Execute("SELECT name, value from {$raw_prefix}inst_languages");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $i = 0;
        while ($debug_query && !$debug_query->EOF)
        {
            $row = $debug_query->fields;
            $avail_lang[$i]['name'] = $row['name'];
            $avail_lang[$i]['value'] = $row['value'];
            $i++;
            $debug_query->MoveNext();
        }
    }

    $maxval = count($avail_lang);

    // If langdir is set on the session, check to see if it is an available language in the game.
    if (isset($_SESSION['langdir']))
    {
        for ($i=0; $i<$maxval; $i++)
        {
            if ($avail_lang[$i]['value'] == $_SESSION['langdir'])
            {
                $templangdir = $_SESSION['langdir'];
                break;
            }
            else
            {
                $templangdir = $default_lang;
            }
        }
    }
    else
    {
        $templangdir = $default_lang;
    }

    return $templangdir;
}
?>
