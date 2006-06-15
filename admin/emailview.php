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
// File: emailview.php

$pos = (strpos($_SERVER['PHP_SELF'], "/emailview.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

$selfpath = basename($_SERVER['PHP_SELF']);
echo "<div align=\"left\">\n";
echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n";
echo "    <tr>\n";
echo "      <td width=\"50%\" nowrap><b>Email Log Viewer</b></td>\n";
echo "    </tr>\n";
echo "  </table>\n";
echo "</div>\n";
echo "<form action=$selfpath method=post>";
echo "<div align=\"center\">\n";
echo '<div style="text-align:center;">\n';
echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n";
echo "    <tr>\n";
echo "      <td width=\"50%\"></td>\n";
echo "      <td width=\"50%\" align=\"right\"><input type=\"submit\" value=\"Clear E-Logs\" name=\"cmd\"><input type=\"submit\" value=\"Refresh\" name=\"cmd\"></td>\n";
echo "            <input type=\"hidden\" name=\"menu\" value=\"emailview\">\n";
echo "    </tr>\n";
echo "  </table>\n";
echo "  </div>\n";
echo "</div>\n";
echo "</form>\n";
if ($cmd == "Clear E-Logs")
{
    $db->Execute("DROP table {$db->prefix}email_log");
    $db->Execute("CREATE table {$db->prefix}email_log(" .
                 "log_id bigint(20) unsigned DEFAulT '0' NOT NulL auto_increment," .
                 "sp_name varchar(50) NOT NulL," .
                 "sp_IP tinytext NOT NulL," .
                 "dp_name varchar(50) NOT NulL," .
                 "e_subject varchar(250)," .
                 "e_status enum('Y','N') DEFAulT 'N' NOT NulL," .
                 "e_type tinyint(3) unsigned DEFAulT '0' NOT NulL," .
                 "e_stamp char(20)," .
                 "e_response varchar(250)," .
                 "PRIMARY KEY (log_id)" .
                 ")");
    $cmd="Refresh";
}

if (empty($cmd)|| $cmd =="Refresh")
{
    echo "<br>\n";
    $res =  $db->Execute ("select * FROM {$db->prefix}email_log");
    echo "<body bgcolor=\"#003\" text=\"#FFF\">\n";
    echo "<div align=\"center\">\n";
    echo '<div style="text-align:center;">\n';
    echo "  <table cellSpacing=\"1\" cellPadding=\"2\" width=\"100%\" bgColor=\"#FC3\" border=\"0\">\n";
    echo "    <tbody>\n";
    echo "      <tr>\n";
    echo "        <td noWrap width=\"50\" bgColor=\"#900\" align=\"left\"><font size=\"2\" color=\"#FFF\"><i><b>ID NO</b></i></font></td>\n";
    echo "        <td noWrap width=\"150\" bgColor=\"#900\" align=\"left\"><font size=\"2\" color=\"#FFF\"><i><b>Source</b></i></font></td>\n";
    echo "        <td noWrap width=\"100\" bgColor=\"#900\" align=\"center\"><font size=\"2\" color=\"#FFF\"><i><b>Source IP</b></i></font></td>\n";
    echo "        <td noWrap width=\"150\" bgColor=\"#900\" align=\"left\"><font size=\"2\" color=\"#FFF\"><i><b>Destination</b></i></font></td>\n";
    echo "        <td noWrap bgColor=\"#900\"><font size=\"2\" color=\"#FFF\"><i><b>Topic&nbsp;</b></i></font></td>\n";
    echo "        <td noWrap width=\"100\" bgColor=\"#900\" align=\"center\"><font size=\"2\" color=\"#FFF\"><i><b>Delivery</b></i></font></td>\n"; 
    echo "        <td noWrap bgColor=\"#900\" align=\"center\"><font size=\"2\" color=\"#FFF\"><i><b>Log Type</b></i></font></td>\n"; 
    echo "        <td noWrap bgColor=\"#900\"><font size=\"2\" color=\"#FFF\"><i><b>Date of Log</b></i></font></td>\n"; 
    echo "        <td noWrap bgColor=\"#900\"><font size=\"2\" color=\"#FFF\"><i><b>Response</b></i></font></td>\n"; 
    echo "      </tr>\n"; 
    while (!$res->EOF)
    {
        $row = $res->fields;
        if ($row['e_type'] == '1')
        {
            $LogType = "Registration";
        }

        if ($row['e_type'] == '2')
        {
            $LogType = "Feedback";
        }

        if ($row['e_type'] == '3')
        {
            $LogType = "PW Request";
        }

        if ($row['e_type'] == '4')
        {
            $LogType = "Debug Info";
        }

        if ($row['e_type'] == '5')
        {
            $LogType = "Global Email";
        }

        if ($row['e_status'] == 'Y')
        {
            $Delivery = "<font size=\"1\" color=\"lime\">Successful</font>";
        }
        else 
        {
            $Delivery = "<font size=\"1\" color=\"red\">Fail</font>"; 
        }

        echo "      <tr>\n"; 
        echo "        <td noWrap width=\"50\" bgColor=\"#009\" align=\"center\"><font size=\"1\" color=\"#FF0\">$row[log_id]</font></td>\n"; 
        echo "        <td noWrap width=\"150\" bgColor=\"#009\" align=\"left\"><font size=\"1\" color=\"#FF0\">$row[sp_name]</font></td>\n"; 
        echo "        <td noWrap width=\"100\" bgColor=\"#009\" align=\"center\"><font size=\"1\" color=\"#FF0\">$row[sp_IP]</font></td>\n"; 
        echo "        <td noWrap width=\"150\" bgColor=\"#009\" align=\"left\"><font size=\"1\" color=\"#FF0\">$row[dp_name]</font></td>\n"; 
        echo "        <td noWrap bgColor=\"#009\"><font size=\"1\" color=\"#FF0\">$row[e_subject]</font></td>\n"; 
        echo "        <td noWrap width=\"100\" bgColor=\"#009\" align=\"center\"><font size=\"1\">$Delivery</font></td>\n"; 
        echo "        <td noWrap bgColor=\"#009\" align=\"center\"><font size=\"1\" color=\"#FF0\">$LogType</font></td>\n"; 
        echo "        <td noWrap bgColor=\"#009\"><font size=\"1\" color=\"#FFF\">$row[e_stamp]</font></td>\n"; 
        echo "        <td noWrap bgColor=\"#009\"><font size=\"1\" color=\"#FFF\">$row[e_response]</font></td>\n"; 
        echo "      </tr>\n"; 
        $res->MoveNext(); 
    } 
echo "</table>\n</div></div>\n"; 
echo "</body>\n"; 
$res = ''; 
} 

?>
