<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: classes/BntDb.php
//
// Class for managing the database inside BNT

if (strpos ($_SERVER['PHP_SELF'], 'BntDb.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

class BntDb
{
    static function initDb ($ADODB_SESSION_CONNECT, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_DRIVER, $db_prefix, $dbport)
    {
        // The data field name "data" violates SQL reserved words - switch it to SESSDATA
        ADODB_Session::dataFieldName ('SESSDATA');

        // Add MD5 encryption for sessions, and then compress it before storing it in the database
        //ADODB_Session::filter (new ADODB_Encrypt_Mcrypt ());
        //ADODB_Session::filter (new ADODB_Compress_Gzip ());

        // If there is a $dbport variable set, use it in the connection method
        if (!empty ($dbport))
        {
            $ADODB_SESSION_CONNECT.= ":$dbport";
        }

        // Attempt to connect to the database
        try
        {
            $db = NewADOConnection($ADODB_SESSION_DRIVER);
            $db_init_result = @$db->Connect ("$ADODB_SESSION_CONNECT", "$ADODB_SESSION_USER", "$ADODB_SESSION_PWD", "$ADODB_SESSION_DB");
            if ($db_init_result === false)
            {
                throw new Exception;
            }
            else
            {
                // We have connected successfully. Now set our character set to utf-8
                $db->Execute ("SET NAMES 'utf8'");

                // Set the fetch mode for database calls to be associative by default
                $db->SetFetchMode (ADODB_FETCH_ASSOC);
            }
        }
        catch (exception $e)
        {
            // We need to display the error message onto the screen.
            $err_msg = "Unable to connect to the " . $ADODB_SESSION_DRIVER . " Database.<br>\n Database Error: ". $db->ErrorNo () .": ". $db->ErrorMsg () ."<br>\n";
            die ($err_msg);
        }

        $db->prefix = $db_prefix;
        // End of database work
        return $db;
    }
}
?>