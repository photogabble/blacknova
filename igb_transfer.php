<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: igb_transfer.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'igb');
load_languages($db, $raw_prefix, 'common');
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'global_includes');

checklogin($db);
get_info($db);
checkdead($db);
$title = $l_igb_title;
include_once ("./header.php");

if (!$allow_ibank)
{
    include_once ("./igb_error.php");
}

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND owner=$playerinfo[player_id]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$planetinfo = $debug_query->RecordCount();

$debug_query = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE base='Y' AND team=$playerinfo[team]");
db_op_result($db,$debug_query,__LINE__,__FILE__);
$teamplanetinfo = $debug_query->RecordCount();

if ($portinfo['port_type'] != 'shipyard' && $portinfo['port_type'] != 'upgrades' && $portinfo['port_type'] != 'devices' && $planetinfo < 1 && $teamplanetinfo < 1)
{
    echo $l_noport . "<br><br>";
    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
else
{
    $no_body = 2;
}

updatecookie($db);

$result = $db->Execute("SELECT * FROM {$db->prefix}ibank_accounts WHERE player_id=$playerinfo[player_id]");
$account = $result->fields;

//echo "<body bgcolor=\"#666\" text=\"#FFFFFF\" link=\"#00FF00\" vlink=\"#00FF00\" alink=\"#FF0000\">";

echo "<style type=\"text/css\">";
echo "    input.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "    select.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}";
echo "</style>";

echo "\n<div style=\"text-align:center;\">";
echo "\n<img alt=\"\" src=\"templates/$templateset/images/div1.png\">";
//echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
//echo "\n<tr><td align=\"center\" background=\"templates/$templateset/images/igbscreen.png\">";

global $playerinfo;
global $account;
global $l_igb_transfertype, $l_igb_toanothership, $l_igb_shiptransfer, $l_igb_fromplanet, $l_igb_source, $l_igb_consolidate;
global $l_igb_unnamed, $l_igb_in, $l_igb_none, $l_igb_planettransfer, $l_igb_back, $l_igb_logout, $l_igb_destination, $l_igb_conspl;
global $db;

$res = $db->Execute("SELECT character_name, player_id FROM {$db->prefix}players ORDER BY character_name ASC");
while (!$res->EOF)
{
    $ships[] = $res->fields;
    $res->MoveNext();
}

$res = $db->Execute("SELECT name, planet_id, sector_id FROM {$db->prefix}planets WHERE owner=$playerinfo[player_id] ORDER BY sector_id ASC");
while (!$res->EOF)
{
    $planets[] = $res->fields;
    $res->MoveNext();
}

echo "\n<form action=\"igb_transfer2.php\" method=\"post\">";
echo "\n<table width=\"600\" height=\"350\" border=\"0\">";
echo "\n  <tr>";
echo "\n    <td align=\"center\" background=\"templates/{$templateset}/images/igbscreen.png\">";
echo "\n      <table width=\"520\" height=\"300\" border=\"0\">";

echo "\n        <tr><td colspan=2 align=\"center\" valign=top><font color=\"#00FF00\">$l_igb_transfertype<br>---------------------------------</font></td></tr>" .
     "\n        <tr valign=top>" .
     "\n          <td>" .
     "<font color=\"#00FF00\">$l_igb_toanothership :<br><br>" .
     "\n            <select class=term name=player_id>";

foreach($ships as $ship)
{
    echo "\n            <option value=$ship[player_id]>$ship[character_name]</option>";
}

echo "\n            </select></font>" .
     "\n          </td>" .
     "\n          <td valign=\"middle\" align=right>" .
     "<input class=term type=submit name=shipt value=\" $l_igb_shiptransfer \">" .
     "</td>" .
     "\n        </tr>" .
     "\n        <tr valign=top>" .
     "\n          <td>" .
     "<br><font color=\"#00FF00\">$l_igb_fromplanet :<br><br>" .
     "$l_igb_source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
     "\n            <select class=term name=splanet_id>";

if (isset($planets))
{
    foreach($planets as $planet)
    {
        if (empty($planet['name']))
        {
            $planet['name'] = $l_igb_unnamed;
        }

        echo "          <option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
}
else
{
    echo "              <option value=none>$l_igb_none</option>";
}

echo "\n            </select><br>$l_igb_destination <select class=term name=dplanet_id>";

if (isset($planets))
{
    foreach($planets as $planet)
    {
        if (empty($planet['name']))
        {
            $planet['name'] = $l_igb_unnamed;
        }

        echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
}
else
{
    echo "<option value=none>$l_igb_none</option>";
}

echo "\n</select></font></td><td valign=\"middle\" align=right>" .
     "\n<br><input class=term type=submit name=planett value=\"$l_igb_planettransfer\">" .
     "\n</td></tr>";

echo "\n<tr valign=bottom>" .
     "\n<td><font color=\"#00FF00\"><a href=\"igb_login.php\">$l_igb_back</a></font></td><td align=right><font color=\"#00FF00\">&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></font></td>" .
     "\n</tr>";
echo "</table></table></form>";

echo "<img alt=\"\" src=\"templates/$templateset/images/div2.png\">";
echo "</div>";

include_once ("./footer.php");
?>
