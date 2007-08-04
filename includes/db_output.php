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
// File: db_output.php
function db_output($db,$status,$served_line,$served_page)
{
    global $langdir, $raw_prefix;
    global $l_db_success, $l_db_failure;

    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    if (!isset($_POST['step']) || $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        // Load language variables
        load_languages($db, $raw_prefix, 'global_includes');
    }

    if ($status)
    {
        $return = "<font color=\"lime\"> - " . $l_db_success . "</font><br>";
    }
    else
    {
        $output_result=str_replace("[served_page]", $served_page, $l_db_failure);
        $output_result=str_replace("[served_line]", $served_line-1, $output_result);
        $output_result=str_replace("[dberror]", $status, $output_result);
//        $output_result=str_replace("[dberror]", $db->ErrorMsg(), $output_result);
        $return = "<font color=\"red\"> - " . $output_result . "<hr>\n</font><br>\n";
    }

    $status ='';
    $output_result = '';
    return $return;
}
?>
