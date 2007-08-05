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
// File: admin/iplog.php

$pos = (strpos($_SERVER['PHP_SELF'], "/iplog.php"));
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

$i = 0;
if ((!isset($_POST['offset'])) || ($_POST['offset'] == ''))
{
    $_POST['offset'] = 0;
}

$offset = $_POST['offset'];

if ((!isset($_POST['submit'])) || ($_POST['submit'] == ''))
{
    $_POST['submit'] = '';
}

$color = $color_line1;
$debug_query = $db->Execute("select time, sector_id as sector, character_name, referer, url, " .
                            "{$db->prefix}ip_log.ip_address, proxy_address from {$db->prefix}ip_log left join " .
                            "{$db->prefix}players on {$db->prefix}ip_log.player_id={$db->prefix}players.player_id");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$log_count = $debug_query->recordcount();

if ($offset >= $log_count)
{
    $offset = $log_count;
}

if ($offset < 0)
{
    $offset = 0;
}

// Check if Left or right is pressed
if (isset($_POST['left']))
{
    $offset -= 10;
}

if (isset($_POST['right']))
{
    $offset += 10;
}

// Verify that offset is valid
if ($offset > ($log_count -10))
{
    $offset = ($log_count - 10);
}

if (!($offset > 0))
{
    $offset = 0;
}

echo $log_count . " Records total.\n<br><br>";
echo "<form action=admin.php method=post>";
echo "<input type=hidden value=$offset name=offset>";
echo "<input type=hidden value=iplog name=menu>";
echo "<input type=submit value=\"&lt&lt\" name=left><input type=submit value=\"&gt&gt\" name=right><br>";
echo "<input type=text value=". $offset ." name=offset>";
echo "<input type=submit value=\"Go direct to row\">";
echo "</form>";

echo "<table border=1 cellspacing=1 cellpadding=4 bgcolor=\"#FFF\">\n";
echo "  <tr bgcolor=\"$color_header\">\n";
echo "    <td><b>time</b></td>\n";
echo "    <td><b>sector</b></td>\n";
echo "    <td><b>character name</b></td>\n";
echo "    <td><b>referer</b></td>\n";
echo "    <td><b>url</b></td>\n";
echo "    <td><b>IP address</b></td>\n";
echo "    <td><b>Proxy address</b></td>\n";
echo "  </tr>\n";

$debug_query->Move($offset);
while(!$debug_query->EOF && (($i + $offset) < $log_count) && ($i < 10))
{
      $ip_log[$i] = $debug_query->fields;
      echo "  <tr bgcolor=\"$color\">\n";
      echo "    <td>". $ip_log[$i]['time'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['sector'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['character_name'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['referer'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['url'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['ip_address'] ."</td>\n";
      echo "    <td>". $ip_log[$i]['proxy_address'] ."</td>\n";
      echo "  </tr>\n";

      if ($color == $color_line1)
      {
          $color = $color_line2;
      }
      else
      {
          $color = $color_line1;
      }

      $debug_query->MoveNext();
      $i++;
}

echo "</table>\n";

//            $sql = "select time, sector_id as sector, character_name, referer, url, {$db->prefix}ip_log.ip_address, proxy_address from {$db->prefix}ip_log left join {$db->prefix}players on {$db->prefix}ip_log.player_id={$db->prefix}players.player_id";
//            $pager = new ADODB_Pager($db,$sql);
//            $pager->Render($rows_per_page=10);

?>
