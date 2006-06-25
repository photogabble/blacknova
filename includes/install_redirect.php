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
// File: inclues/install_redirect.php

function install_redirect($ADODB_SESSION_DRIVER)
{
    if ($_SERVER['SERVER_PORT'] == '443')
    {
        $server_type = 'https';
    }
    else
    {
        $server_type = 'http';
    }

    if (empty($ADODB_SESSION_DRIVER))
    {
        if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
        {
            $add_slash_to_url = '/';
        }

        $server_port = '';
        if ($_SERVER['SERVER_PORT'] != '80' || $_SERVER['SERVER_PORT'] != '443')
        {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }

        echo $server_type;
        // Much smoother - no broken header/footer issues, and seamless for user.
//        header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] .$server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "install.php");
        exit();
    }
}
?>
