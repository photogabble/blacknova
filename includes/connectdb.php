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
// File: includes/connectdb.php

function connectdb($adodb_session_db, $adodb_session_driver, $adodb_session_user, $adodb_session_pwd, $adodb_session_connect, $dbport, $raw_prefix)
{
    if (!empty($dbport))
    {
        $adodb_session_connect.= ':'. $dbport;
    }

    $db = ADONewConnection("$adodb_session_driver");
    $result = $db->Connect($adodb_session_connect, $adodb_session_user, $adodb_session_pwd, $adodb_session_db);

    if (!$result)
    {
        //    $title = $l_error_occured;
        die ("Unable to connect to the database: ");
        //    include_once ("./footer.php");
    }
    else
    {
        $db->debug = 0;
        $db->autoRollback = true; // Shouldnt this be false?!
        $db->prefix = $raw_prefix;
        return $db;
    }
}
?>
