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
// File: sectedit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/sectedit.php"));
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

echo "<h2>Sector editor</h2>";
echo "<form action=admin.php method=post>";
if (empty($_POST['sector']))
{
    echo "<select size=20 name=sector>";
    $res = $db->Execute("select sector_id FROM {$db->prefix}universe ORDER BY sector_id");
    while (!$res->EOF)
    {
        $row = $res->fields;
        if ($row['sector_id'] > 2)
        {
            echo "<option value=$row[sector_id]> $row[sector_id] </option>";
        }
        $res->MoveNext();
    }
    echo "</select>";
    echo "&nbsp;<input type=submit value=Edit>";
}
else
{
    $sector = $_POST['sector'];
    if (empty($_POST['operation']))
    {
        $res = $db->Execute("select * FROM {$db->prefix}universe WHERE sector_id=?", array($sector));
        $row = $res->fields;
        echo "<table border=1 cellspacing=2 cellpadding=2>";
        echo "<tr><td><tt>          Sector ID  </tt></td><td><font color=#6F0>$sector</font></td>";
        echo "<td align=right><tt>  Sector name</tt></td><td><input type=text size=15 name=sector_name value=\"$row[sector_name]\"></td>";
        echo "<td align=right><tt>  Star size</tt></td><td><input type=text size=15 name=star_size value=\"$row[star_size]\"></td>";
        echo "<td align=right><tt>  Zone ID    </tt></td><td>";
        echo "<select size=1 name=zone_id>";
        $ressubb = $db->Execute("select zone_id,zone_name FROM {$db->prefix}zones ORDER BY zone_name");
        while (!$ressubb->EOF)
        {
            $rowsubb = $ressubb->fields;
            if ($rowsubb[zone_id] == $row[zone_id])
            {
                echo "<option selected=$rowsubb[zone_id] value=$rowsubb[zone_id]>$rowsubb[zone_name]</option>";
            }
            else
            {
                echo "<option value=$rowsubb[zone_id]>$rowsubb[zone_name]</option>";
            }

            $ressubb->MoveNext();
        }

        echo "</select></td></tr>";
        echo "<tr><td><tt>          X   </tt></td><td><input type=text size=9 name=x value=\"$row[x]\"></td>";
        echo "<td align=right><tt>  Y     </tt></td><td><input type=text size=9 name=y value=\"$row[y]\"></td>";
        echo "<td align=right><tt>  Z     </tt></td><td><input type=text size=9 name=z value=\"$row[z]\"></td></tr>";
        echo "<tr><td colspan=6>    <hr>       </td></tr>";
        echo "</table>";

        echo "<table border=5 cellspacing=2 cellpadding=2>";
        echo "<tr><td><tt>          Port Type  </tt></td><td>";
        echo "<select size=1 name=port_type>";
        $oportnon = $oportorg = $oportore = $oportgoo = $oportene = "value";

        if ($row[port_type] == "none")
        {
            $oportnon = "selected=none value";
        }

        if ($row[port_type] == "organics")
        {
            $oportorg = "selected=organics value";
        }

        if ($row[port_type] == "ore")
        {
            $oportore = "selected=ore value";
        }

        if ($row[port_type] == "goods")
        {
            $oportgoo = "selected=goods value";
        }

        if ($row[port_type] == "energy")
        {
            $oportene = "selected=energy value";
        }

        echo "<option $oportnon=none>none</option>";
        echo "<option $oportorg=organics>organics</option>";
        echo "<option $oportore=ore>ore</option>";
        echo "<option $oportgoo=goods>goods</option>";
        echo "<option $oportene=energy>energy</option>";
        echo "</select></td>";
        echo "<td align=right><tt>  Organics   </tt></td><td><input type=text size=9 name=port_organics value=\"$row[port_organics]\"></td>";
        echo "<td align=right><tt>  Ore        </tt></td><td><input type=text size=9 name=port_ore value=\"$row[port_ore]\"></td>";
        echo "<td align=right><tt>  Goods      </tt></td><td><input type=text size=9 name=port_goods value=\"$row[port_goods]\"></td>";
        echo "<td align=right><tt>  Energy     </tt></td><td><input type=text size=9 name=port_energy value=\"$row[port_energy]\"></td></tr>";
        echo "<tr><td colspan=10>   <hr>       </td></tr>";
        echo "</table>";
        echo "<br>";
        echo "<input type=hidden name=sector value=$sector>";
        echo "<input type=hidden name=operation value=save>";
        echo "<input type=submit size=1 value=save>";
    }
    elseif ($_POST['operation'] == "save")
    {
        // update database
        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET sector_name=?, zone_id=?, " .
                                    "star_size=?, port_type=?, port_organics=?, " .
                                    "port_ore=?, port_goods=?, port_energy=?, " .
                                    "z=?,x=?,y=? WHERE sector_id=?", array($_POST['sector_name'], $_POST['zone_id'], $_POST['star_size'], $_POST['port_type'], $_POST['port_organics'], $_POST['port_ore'], $_POST['port_goods'], $_POST['port_energy'], $_POST['z'], $_POST['x'], $_POST['y'], $sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo "<input type=submit value=\"Return to Sector editor\">";
    }
    else
    {
        echo "Invalid operation";
    }
}
echo "<input type=hidden name=menu value=sectedit>";
echo "</form>";

?>
