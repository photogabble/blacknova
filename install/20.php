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
// File: install/20.php

$safe_mode = @ini_get("safe_mode");

// Used for file integrity check
if (!function_exists("file_get_contents"))
{
    // Dynamic functions
    dynamic_loader ($db, "file_get_contents.php");
}

// Safe mode doesn't allow direct file writing.
if ($safe_mode)
{
    echo "The machine you are running this script on appears to have \"safe_mode\" enabled.\n<br>";
    echo "This means that the automatic portions of this script won't function properly.\n<br>";
    echo "Instead of automatically configuring the game, we will generate a file that you can upload via ftp.\n<br><br>";
}

if ($game_installed && $swordfish != $adminpass)
{
    $md5list = file_get_contents("md5sum_list", $use_include_path = 0);

    $md5new = explode("  ", $md5list);
    $k = 1;
    for ($i = 0; $i < count($md5new) ; $i++)
    {
        $part = $md5new[$i];
        $md5newer = explode("\n", $md5new[$i]);
        for ($j = 0; $j < count($md5newer) ; $j++)
        {
            $kimble[$k] = $md5newer[$j];
            $k++;
        }
    }

    for ($temp = 1; $temp< $k-1; $temp=$temp+2)  
    {
        if (md5_file($kimble[$temp+1]) != $kimble[$temp])
        {
            echo "<br><font color=red>The following file DOES NOT match the checksums it shipped with, and may be corrupted!</font>";
            echo "<br><font color=yellow>You may want to try redownloading it.</font>";
            echo "<br>Filename: ";
            echo $kimble[$temp+1];
            echo "<br>Checksum it shipped with: ";
            echo $kimble[$temp];
            echo "<br>Actual md5sum: ";
            echo md5_file($kimble[$temp+1]);
            echo "<br><br>";    
        }
    }

    echo "It seems that you have already installed the game. If you want to edit your 'db_config.php' in /config/ dir, enter your admin password: ";
    echo "<form action=\"install.php\" method=\"post\"><input type=password name=swordfish value=\"\">&nbsp;";
    echo "<input type=submit value=\"Submit\"></form>";
    echo "Everything looks great! Feel free to run the <a href=\"./make_galaxy.php\">Make Galaxy</a> script now!";
    $showit = 0;
}

if ($showit == 1)     // Preparing values for the form
{
    $v[1]  = isset($ADODB_SESSION_DRIVER) ? $ADODB_SESSION_DRIVER : 'mysqlt';
    $v[2]  = isset($ADODB_SESSION_DB) ? $ADODB_SESSION_DB : '';
    $v[3]  = isset($ADODB_SESSION_USER) ? $ADODB_SESSION_USER : '';
    $v[4]  = isset($ADODB_SESSION_PWD) ? $ADODB_SESSION_PWD : '';
    $v[5]  = isset($ADODB_SESSION_CONNECT) ? $ADODB_SESSION_CONNECT : 'localhost';
    $v[6]  = isset($dbport) ? $dbport : '3306';
    $v[8]  = isset($raw_prefix) ? $raw_prefix : 'bnt_';
    $v[14] = isset($adminpass) ? $adminpass : '';

    if (isset($ADODB_CRYPT_KEY))
    {
        $v[17] = $ADODB_CRYPT_KEY;
    }
    else
    {
        $mykey='';
        mt_srand((double)microtime()*1000000);
        for ($i=0; $i<16; $i++)
        {
            $mykey .= chr(mt_rand(97,122));
        }
        $v[17] = $mykey;
    }

    $v[18] = isset($server_type) ? $server_type : 'http';

    echo "<script type=\"text/javascript\" src=\"backends/javascript/installtips.js\"></script>";
    echo "<form action=\"install.php\"  method=\"post\"><table>";

    echo "<tr><td>Database type&nbsp;<a href='#' onclick=\"mytip('0')\">?</a></td>";
    echo "<td><select tabindex=1 name=_ADODB_SESSION_DRIVER>";
    foreach($dbs as $value => $name)
    {
        echo "<option value=$value " . ($v[1] == $value ? 'selected' : '') . ">$name</option>";
    }

    echo "</select></td></tr>";

    echo "<tr><td>Database name&nbsp;<a href='#' onclick=\"mytip('1')\">?</a></td><td><input tabindex=2 type=text name=_ADODB_SESSION_DB value=\"$v[2]\"></td></tr>";
    echo "<tr><td>Database username&nbsp;<a href='#' onclick=\"mytip('2')\">?</a></td><td><input tabindex=3 type=text name=_ADODB_SESSION_USER value=\"$v[3]\"></td></tr>";
    echo "<tr><td>Database password&nbsp;<a href='#' onclick=\"mytip('2')\">?</a></td><td><input tabindex=4 type=password name=_ADODB_SESSION_PWD value=\"$v[4]\"></td></tr>";
    echo "<tr><td><strong>Database host</strong>&nbsp;<a href='#' onclick=\"mytip('3')\">?</a></td><td><input tabindex=5 type=text name=_ADODB_SESSION_CONNECT value=\"$v[5]\"></td></tr>";
    echo "<tr><td><strong>Database port</strong>&nbsp;<a href='#' onclick=\"mytip('3')\">?</a></td><td><input tabindex=6 type=text name=_dbport value=\"$v[6]\"></td></tr>";
    echo "<tr><td>Database table prefix&nbsp;<a href='#' onclick=\"mytip('5')\">?</a></td><td><input tabindex=8 type=text name=_raw_prefix value=\"$v[8]\"></td></tr>";
    echo "<tr><td>Admin password&nbsp;<a href='#' onclick=\"mytip('10')\">?</a></td><td><input tabindex=15 type=password name=_adminpass value=\"$v[14]\"></td></tr>";
    echo "<tr><td>Confirm admin password&nbsp;<a href='#' onclick=\"mytip('11')\">?</a></td><td><input tabindex=16 type=password name=adminpass2 value=\"$v[14]\"></td></tr>";
    echo "<tr><td><strong>Session crypt key</strong>&nbsp;<a href='#' onclick=\"mytip('14')\">?</a></td><td><input tabindex=21 type=text name=_ADODB_CRYPT_KEY value=\"$v[17]\"></td></tr>";
    echo "<tr><td><strong>Server type</strong>&nbsp;<a href='#' onclick=\"mytip('18')\">?</a></td><td><input tabindex=22 type=text name=_server_type value=\"$v[18]\"></td></tr>";
    echo "<tr><td><input type=hidden name=\"step\" value=\"2\"></td></tr>";
    echo "<tr><td><input type=hidden name=\"swordfish\" value=\"$swordfish\"></td></tr>";
    echo "<tr><td><input tabindex=22 type=\"submit\" value=\"Submit\" onclick=\"validate()\"></td><td></td></tr>";
    echo "</table></form><br><br>";
}
?>
