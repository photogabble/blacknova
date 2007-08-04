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
// File: global_cleanups.php

//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Set the server type
if ($_SERVER['SERVER_PORT'] == '443')
{
    $server_type = 'https';
}
else
{
    $server_type = 'http';
}

ini_set('arg_separator.output', '&amp;'); // Ensures that all ampersands are html-compliant, if php appends session id's to the url (instead of via cookie)
ini_set('url_rewriter.tags', ''); // Ensure that the session id is *not* passed on the url - this is a big security hole for logins - including admin.

fix_magic_quotes(); // See the function in common_functions. This fixes all possible weirdness from magic_quotes_*


$db = '';
$pos = (strpos($_SERVER['PHP_SELF'], "/install.php"));
if (!$pos)
{
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

        // Much smoother - no broken header/footer issues, and seamless for user.
        header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] . $server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "install.php");
        exit();
    }

    global $ADODB_COUNTRECS, $ADODB_NEVER_PERSIST;
    $ADODB_NEVER_PERSIST   = TRUE; // Prevent any persistent connections ever.
    $ADODB_COUNTRECS = FALSE; // This *deeply* improves the speed of adodb.

    global $db;
    $db = connectdb($ADODB_SESSION_DB, $ADODB_SESSION_DRIVER, $ADODB_SESSION_USER, $ADODB_SESSION_PWD, $ADODB_SESSION_CONNECT, $dbport);
    
    if (!$db)
    {
        //    $title = $l_error_occured;
        die ("Unable to connect to the database: ");
        echo "Cannot connect to database.";
        //    include_once ("./footer.php");
        die();
    }

    $db->debug = 0;
    $db->autoRollback = true;
    $db->prefix = $raw_prefix;

    // Now get the server config from the permanent root (prot) context.
    // Ensure that the serverconfig table has been created, and if so, get the serverconfig values from it.
    $debug_query = $db->Execute("SELECT name,value FROM {$raw_prefix}serverconfig");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    // Get the config_values from the DB
    while ($debug_query && !$debug_query->EOF)
    {
        $row = $debug_query->fields;
        $$row['name'] = $row['value'];
        $debug_query->MoveNext();
    }

    // After step 2 of make galaxy, the sessions table has been created, so, start a session.
    // Also - Sessions are on the raw_prefix - allowing cross game support
    if (!isset($_POST['step']) || $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        // We explicitly use encrypted sessions, but this adds compression as well.
        $ADODB_SESSION_TBL     = $raw_prefix."sessions";
        ADODB_Session::encryptionKey($ADODB_CRYPT_KEY);
//        ADODB_Session::filter(new ADODB_Compress_GZip());

        // The data field name "data" violates SQL reserved words - switch it to session_data.
        ADODB_Session::dataFieldName('session_data');
        if (!isset($_SESSION))
        {
            session_start();
        }
    }

    if (isset($_POST['gamenum']))
    {
        $db->prefix = $raw_prefix . $_POST['gamenum']. "_";
    }
    elseif (isset($_SESSION['game']))
    {
        $db->prefix = $_SESSION['game'];
    }
    else
    {
        $db->prefix = $raw_prefix;
    }

    if ($db->prefix != $raw_prefix)
    {
        // Get the config_values from the DB
        $debug_query = $db->Execute("SELECT name,value FROM {$db->prefix}config_values");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        while (!$debug_query->EOF && $debug_query)
        {
            $row = $debug_query->fields;
            $$row['name'] = $row['value'];
            $debug_query->MoveNext();
        }

        // Since we now have the config values(including perf_logging), if the admin wants perf logging on - turn it on.
        if (isset($perf_logging) && $perf_logging)
        {
            $debug_query = $db->SelectLimit("SELECT * from {$db->prefix}adodb_logsql",1);
            if ($debug_query)
            {
                adodb_perf::table("{$db->prefix}adodb_logsql");
                $db->LogSQL();
            }
        }
    }
}

$templateset = 'classic'; // This needs to be dynamically set by the user

$langdir = set_langdir($db,$pos);

dynamic_loader ($db, "load_languages.php");
load_languages($db, $raw_prefix, 'regional');

// Template Lite
$template = new bnt_smarty;
?>
