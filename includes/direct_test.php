<?php
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
// File: inclues/direct_test.php

function direct_test($file, $phpself)
{
    echo "direct test";
    die();
    // Soon, we can template this!
    global $langdir, $l_error_occured, $l_cannot_access, $raw_prefix;
    $phpfile = substr($file, (strrpos($file, "/") +1));
    $selffile = substr($phpself, (strrpos($phpself, "/") +1));

    if ($phpfile == $selffile)
    {
        include_once './global_includes.php';
        dynamic_loader ($db, "load_languages.php");

        // Load language variables
        load_languages($db, $raw_prefix, 'common');

        $title = $l_error_occured;
        echo $l_cannot_access;
        include_once './footer.php';
        die();
    }
}
?>
