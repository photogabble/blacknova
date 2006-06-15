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
// File: install/30.php

    if ($_POST['_ADODB_SESSION_DB'] == '')
    {
        echo "<strong>Database name cannot be empty! <a href=\"install.php\">Try again</a>.</strong>";
    }
    elseif ($_POST['_adminpass'] == '')
    {
        echo "<strong>Admin password cannot be empty! <a href=\"install.php\">Try again</a>.</strong>";
    }
    elseif ($_POST['_adminpass'] != $_POST['adminpass2'])
    {
        echo "<strong>Admin passwords don't match! <a href=\"install.php\">Try again</a>.</strong>";
    }
    elseif ($_POST['_ADODB_CRYPT_KEY'] == '')
    {
        echo "<strong>Session crypt key cannot be empty! <a href=\"install.php\">Try again</a>.</strong>";
    }
    elseif ($_POST['_server_type'] == '')
    {
        echo "<strong>Server type cannot be empty! <a href=\"install.php\">Try again</a>.</strong>";
    }
    else
    {
        clearstatcache();
        $old_time = @filemtime('config/db_config.php');
        // Writing the config file directly
        $fs = @fopen('config/db_config.php', 'w+');

        $data = '';
        $data .= "<?php\n";
        $data .= "// Automatically created configuration file. Do not change!\n\n";
        $data .= '$pos' . " = strpos(";
        $data .= '$_SERVER[\'PHP_SELF\']';
        $data .= ', "/db_config.php");';
        $data .= "\nif (" . '$pos' . ' !== false)';
        $data .= "\n{";
        $data .= "\n    echo \"You can not access this file directly!\";";
        $data .= "\n    die();";
        $data .= "\n}\n\n";
        foreach($_POST as $key => $value)
        {
            if (substr($key, 0, 1) == '_' )
            {
                $key = substr($key, 1);
                $data .= "\$$key = \"$value\";\n";
            }
        }
        $data .= "\n";
        $data .= "?>\n";
        @fwrite($fs, $data);
        @fclose($fs);

        clearstatcache();
        $new_time = filemtime('config/db_config.php');
        if ($old_time == $new_time)   // The file is not changed automatically -- error
        {
            $rawdata = rawurlencode($data);
            docheck($old_time);
        }
        else
        {
            docheck(0);
        }
    }

// If the file config/db_config.php does not allow writing, admin gets the download form
function docheck($time_error)
{
    global $rawdata;

    include ("./config/db_config.php");   // Checking just created config file

    $game_installed = isset($ADODB_CRYPT_KEY);
    if ($game_installed && !$time_error)
    {
        echo "<font color=lime>Local settings successfully saved.</font><br><br>";
        echo "Please change the permissions on your db_config.php right away!<br>";
        echo "Set it to '0404', or user = R, group = ---, other = R--<br>";
        echo "<font color=\"red\">If you do not, you risk having your server compromised!!</font><br>";
        echo "make_galaxy.php will not run until you change that permission.<br><br>";
        echo "Everything looks great! Feel free to run the <a href=\"./make_galaxy.php\">Create Universe</a> script now!";
        echo "<br><br>If you have already run the make galaxy script, <a href=\"index.php\">Login now</a>.<br><br>";
    }
    else
    {
        echo "<script type=\"text/javascript\" defer=\"defer\">";
        echo "function enablebutton()";
        echo "{";
        echo "    document.forms[1].secondbutton.disabled=false;";
        echo "}";
        echo "</script>";

        echo "<font color=red><strong>ERROR:</strong><br>Local settings are <strong>NOT</strong> successfully saved.</font>";

        echo "<br><br><form action=\"install.php\" method=\"post\"><input type=\"hidden\" name=\"rawdata\" value=\"$rawdata\">";
        echo "<input type=hidden name=\"step\" value=\"3\"><input type=submit value=\"Download\" onclick=\"enablebutton()\"> ";
        echo "the file 'db_config.php' and upload it manually to <strong>/config</strong> subdir (overwrite the current one).</form>";

        echo "<form action=\"install.php\" method=\"post\"><strong>AFTER</strong> that click <input type=\"hidden\" name=\"rawdata\" value=\"$rawdata\">";
        echo "<input type=\"hidden\" name=\"step\" value=\"4\"><input type=\"hidden\" name=\"_adminpass\" value=\"$_POST[_adminpass]\">";
        echo "<input type=\"hidden\" name=\"time_error\" value=\"$time_error\"><input type=\"submit\" name=\"secondbutton\" value=\"here.\" disabled=\"disabl$
    }
}
?>
