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
// File: traderoute_new.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

global $playerinfo, $shipinfo;
global $num_traderoutes;
global $max_traderoutes_player;
global $l_tdr_editerr, $l_tdr_maxtdr, $l_tdr_createnew, $l_tdr_editinga, $l_tdr_traderoute, $l_tdr_unnamed;
global $l_tdr_cursector, $l_tdr_selspoint, $l_tdr_port, $l_tdr_planet, $l_tdr_none, $l_tdr_insector, $l_tdr_selendpoint;
global $l_tdr_selmovetype, $l_tdr_realspace, $l_tdr_warp, $l_tdr_selcircuit, $l_tdr_oneway, $l_tdr_bothways, $l_tdr_create;
global $l_tdr_modify, $l_tdr_returnmenu, $l_tdr_none;
global $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks, $l_team;
global $db, $editroute;

if (!empty($_GET['traderoute_id']))
{
    $result = $db->Execute("SELECT * FROM {$db->prefix}traderoutes WHERE traderoute_id=$_GET[traderoute_id]");
    if (!$result || $result->EOF)
    {
        traderoute_die($l_tdr_editerr);
    }

    $editroute = $result->fields;
    if ($editroute['owner'] != $playerinfo['player_id'])
    {
        traderoute_die($l_tdr_notowner);
    }
}

if ($num_traderoutes >= $max_traderoutes_player && empty($editroute))
{
    traderoute_die("<p>$l_tdr_maxtdr<p>");
}

echo "<strong>";

if (empty($editroute))
{
    echo $l_tdr_createnew;
}
else
{
    echo $l_tdr_editinga . " ";
}

echo $l_tdr_traderoute . "</strong><p>";

//---------------------------------------------------
//---- Get Planet info team and Personal (BEGIN) ----

$result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE owner=$playerinfo[player_id] ORDER BY sector_id");

$num_planets = $result->RecordCount();
$i = 0;
while (!$result->EOF)
{
    $planets[$i] = $result->fields;
    if ($planets[$i]['name'] == "")
    {
        $planets[$i]['name'] = $l_tdr_unnamed;
    }

    $i++;
    $result->MoveNext();
}

$result = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE team=$playerinfo[team] AND team!=0 AND owner!=$playerinfo[player_id] ORDER BY sector_id");
$num_team_planets = $result->RecordCount();
$i = 0;
while (!$result->EOF)
{
    $planets_team[$i] = $result->fields;
    if ($planets_team[$i]['name'] == "")
    {
        $planets_team[$i]['name'] = $l_tdr_unnamed;
    }

    $i++;
    $result->MoveNext();
}

//---- Get Planet info team and Personal (END) ------
//---------------------------------------------------

// Display Current Sector
echo $l_tdr_cursector . " " . $shipinfo['sector_id'] . "<br>";

echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";

// Start of form for starting location
echo "<form action=\"traderoute.php?command=create\" method=\"post\">";
echo "<table border=\"0\"><tr>";
echo "<td align=\"right\"><strong>" . $l_tdr_selspoint . "<br>&nbsp;</strong></td>";
echo "<tr>";
echo "<td align=\"right\">" . $l_tdr_port . " : </td>";
echo "<td><input type=\"radio\" name=\"ptype1\" value=\"port\"";

if (empty($editroute) || (!empty($editroute) && $editroute['source_type'] == 'P'))
{
    echo " checked";
}

echo "></td>";
echo "<td>&nbsp;&nbsp;<input type=\"text\" name=\"port_id1\" size=\"20\" align=\"middle\"";

if (!empty($editroute) && $editroute['source_type'] == 'P')
{
    echo " value=\"$editroute[source_id]\"";
}
else
{
    echo " value=\"$shipinfo[sector_id]\"";
}

echo "></td></tr><tr>";

//-------------------- Personal Planet
echo "<td align=\"right\">Personal " . $l_tdr_planet . " : </td>";
echo "<td><input type=\"radio\" name=\"ptype1\" value=\"planet\"";

if (!empty($editroute) && $editroute['source_type'] == 'L')
{
    echo " checked";
}

echo "></td>";
echo "<td>&nbsp;&nbsp;<select name=\"planet_id1\">";

if ($num_planets == 0)
{
    echo "<option value=\"none\">" . $l_tdr_none . "</option>";
}
else
{
    $i = 0;
    while ($i < $num_planets)
    {
        echo "<option ";
        if ($planets[$i]['planet_id'] == $editroute['source_id'])
        {
            echo "selected ";
        }

        echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " $l_tdr_insector " . $planets[$i]['sector_id'] . "</option>";
        $i++;
    }
}

echo "</select></tr>";

//----------------------- team Planet
echo "<tr><td align=\"right\">" . $l_team . " " . $l_tdr_planet . " : </td>";
echo "<td><input type=\"radio\" name=\"ptype1\" value=\"team_planet\"";

if (!empty($editroute) && $editroute['source_type'] == 'C')
{
    echo " checked";
}

echo '></td><td>&nbsp;&nbsp;<select name=team_planet_id1>';

if ($num_team_planets == 0)
{
    echo "<option value=none>$l_tdr_none</option>";
}
else
{
    $i = 0;
    while ($i < $num_team_planets)
    {
        echo "<option ";
        if ($planets_team[$i][planet_id] == $editroute[source_id])
        {
            echo "selected ";
        }

        echo "value=" . $planets_team[$i][planet_id] . ">" . $planets_team[$i][name] . " $l_tdr_insector " . $planets_team[$i][sector_id] . "</option>";
        $i++;
    }
}
    echo "
    </select>
    </tr>";
    //----------------------- End Start point selection

    //----------------------- Begin Ending point selection
    echo "
    <tr><td>&nbsp;
    </tr><tr>
    <td align=right><strong>$l_tdr_selendpoint : <br>&nbsp;</strong></td>
    <tr>
    <td align=right>$l_tdr_port : </td>
    <td><input type=radio name=\"ptype2\" value=\"port\"
    ";

    if (empty($editroute) || (!empty($editroute) && $editroute['dest_type'] == 'P'))
    {
        echo " checked";
    }

    echo '
    ></td>
    <td>&nbsp;&nbsp;<input type=text name=port_id2 size=20 align=middle
    ';

    if (!empty($editroute) && $editroute['dest_type'] == 'P')
    {
        echo " value=\"$editroute[dest_id]\"";
    }

    echo "
    ></td>
    </tr>";

    //-------------------- Personal Planet
    echo "
    <tr>
    <td align=right>Personal $l_tdr_planet : </td>
    <td><input type=radio name=\"ptype2\" value=\"planet\"
    ";

    if (!empty($editroute) && $editroute['dest_type'] == 'L')
    {
        echo " checked";
    }

    echo '
    ></td>
    <td>&nbsp;&nbsp;<select name=planet_id2>
    ';

    if ($num_planets == 0)
    {
        echo "<option value=none>$l_tdr_none</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_planets)
        {
            echo "<option ";
            if ($planets[$i]['planet_id'] == $editroute['dest_id'])
            {
                echo "selected ";
            }

            echo "value=" . $planets[$i]['planet_id'] . ">" . $planets[$i]['name'] . " $l_tdr_insector " . $planets[$i]['sector_id'] . "</option>";
            $i++;
        }
    }

    echo "
    </select>
    </tr>";

    //----------------------- team Planet
    echo "
    <tr>
    <td align=right>$l_team $l_tdr_planet : </td>
    <td><input type=radio name=\"ptype2\" value=\"team_planet\"
    ";

    if (!empty($editroute) && $editroute['dest_type'] == 'C')
    {
        echo " checked";
    }

    echo '
    ></td>
    <td>&nbsp;&nbsp;<select name=team_planet_id2>
    ';

    if ($num_team_planets == 0)
    {
        echo "<option value=none>$l_tdr_none</option>";
    }
    else
    {
        $i=0;
        while ($i < $num_team_planets)
        {
            echo "<option ";
            if ($planets_team[$i][planet_id] == $editroute[dest_id])
            {
                echo "selected ";
            }

            echo "value=" . $planets_team[$i][planet_id] . ">" . $planets_team[$i][name] . " $l_tdr_insector " . $planets_team[$i][sector_id] . "</option>";
            $i++;
        }
    }

    echo "
    </select>
    </tr>";
    //----------------------- End finishing point selection

    echo "
    <tr>
    <td>&nbsp;
    </tr><tr>
    <td align=right><strong>$l_tdr_selmovetype : </strong></td>
    <td colspan=2 valign=top><input type=radio name=\"move_type\" value=\"realspace\"
    ";

    if (!empty($editroute) && $editroute['move_type'] == 'R')
    {
        echo " checked";
    }

    echo "
    >&nbsp;$l_tdr_realspace&nbsp;&nbsp;<input type=radio name=\"move_type\" value=\"warp\"
    ";

    if (empty($editroute) || (!empty($editroute) && $editroute['move_type'] == 'W'))
    {
        echo " checked";
    }

    echo "
    >&nbsp;$l_tdr_warp</td>
    </tr><tr>
    <td align=right><strong>$l_tdr_selcircuit : </strong></td>
    <td colspan=2 valign=top><input type=radio name=\"circuit_type\" value=\"1\"
    ";

    if (!empty($editroute) && $editroute['circuit'] == '1')
    {
        echo " checked";
    }

    echo "
    >&nbsp;$l_tdr_oneway&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name=\"circuit_type\" value=\"2\"
    ";

    if (empty($editroute) || (!empty($editroute) && $editroute['circuit'] == '2'))
    {
        echo " checked";
    }

    echo "
    >&nbsp;$l_tdr_bothways</td>
    </tr><tr>
    <td>&nbsp;
    </tr><tr>
    <td><td><td align=center>
    ";

    if (empty($editroute))
    {
        echo "<input type=submit value=\"$l_tdr_create\" onclick=\"clean_forms()\">";
    }
    else
    { 
        echo "<input type=hidden name=editing value=$editroute[traderoute_id]>";
        echo "<input type=submit value=\"$l_tdr_modify\" onclick=\"clean_forms()\">";
    }

    echo "
    </table>
    <a href=traderoute.php>$l_tdr_returnmenu</a><br>
    </form>
    ";

    global $l_global_mmenu;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
?>
