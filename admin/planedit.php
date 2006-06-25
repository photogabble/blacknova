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
// File: admin/planedit.php

$pos = (strpos($_SERVER['PHP_SELF'], "/planedit.php"));
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

echo "<h3>Planet editor</h3>";
echo "<form action=admin.php method=post>";
if (empty($_POST['planet']))
{
    echo "<select size=15 name=planet>";
    $res = $db->Execute("select planet_id, name, sector_id, character_name FROM {$db->prefix}planets LEFT JOIN " .
                        "{$db->prefix}players ON {$db->prefix}planets.owner = {$db->prefix}players.player_id ORDER BY " .
                        "sector_id");
    while (!$res->EOF)
    {
        $row = $res->fields;
        if ($row['name'] == "")
        {
            $row['name'] = "Unnamed";
        }

        if ($row['character_name'] == "") // was 'null' changed due to weird cAsE
        {
            $row['character_name'] = "No one";
        }

        echo "<option value=$row[planet_id]> $row[name] in sector $row[sector_id], owned by $row[character_name]</option>\n";
        $res->MoveNext();
    }

    echo "</select>";
    echo "&nbsp;<input type=submit value=Edit>";
}
else
{
$planet = $_POST['planet'];
    if (empty($_POST['operation']))
    {
        $res = $db->Execute("select * FROM {$db->prefix}planets WHERE planet_id=?", array($planet));
        $row = $res->fields;

        echo "<table border=1 cellspacing=2 cellpadding=2>";
        echo "<tr><td><tt>          Planet ID  </tt></td><td><font color=#6F0>$planet</font></td>";
        echo "<td align=right><tt>  Sector ID  </tt><input type=text size=5 name=sector_id value=\"$row[sector_id]\"></td>";
        echo "<td align=right><tt>  Defeated   </tt><input type=checkbox name=defeated value=ON " . checked($row['defeated']) . "></td></tr>";
        echo "<tr><td><tt>          Planet name</tt></td><td><input type=text size=15 name=name value=\"$row[name]\"></td>";
        echo "<td align=right><tt>  Base       </tt><input type=checkbox name=base value=ON " . checked($row['base']) . "></td>";
        echo "<td align=right><tt>  Sells      </tt><input type=checkbox name=sells value=ON " . checked($row['sells']) . "></td></tr>";
        echo "<tr><td colspan=4>    <hr>       </td></tr>";
        echo "</table>";
        echo "<table border=1 cellspacing=2 cellpadding=2>";
        echo "<tr><td><tt>          Planet Owner</tt></td><td>";
        echo "<select size=1 name=owner>";
        $ressuba = $db->Execute("select player_id,character_name FROM {$db->prefix}players ORDER BY character_name");
        echo "<option value=0>No One</option>";
        while (!$ressuba->EOF)
        {
            $rowsuba = $ressuba->fields;
            if ($rowsuba['player_id'] == $row['owner'])
            {
                echo "<option selected=$rowsuba[player_id] value=$rowsuba[player_id]>$rowsuba[character_name]</option>";
            }
            else
            {
                echo "<option value=$rowsuba[player_id]>$rowsuba[character_name]</option>";
            }

            $ressuba->MoveNext();
        }

        echo "</select></td>";
        echo "<td align=right><tt>  Organics   </tt></td><td><input type=text size=9 name=organics value=\"$row[organics]\"></td>";
        echo "<td align=right><tt>  Ore        </tt></td><td><input type=text size=9 name=ore value=\"$row[ore]\"></td>";
        echo "<td align=right><tt>  Goods      </tt></td><td><input type=text size=9 name=goods value=\"$row[goods]\"></td>";
        echo "<td align=right><tt>  Energy     </tt></td><td><input type=text size=9 name=energy value=\"$row[energy]\"></td></tr>";
        echo "<tr><td><tt>          Planet Team</tt></td><td><input type=text size=5 name=team value=\"$row[team]\"></td>";
        echo "<td align=right><tt>  Colonists  </tt></td><td><input type=text size=9 name=colonists value=\"$row[colonists]\"></td>";
        echo "<td align=right><tt>  Credits    </tt></td><td><input type=text size=9 name=credits value=\"$row[credits]\"></td>";
        echo "<td align=right><tt>  Fighters   </tt></td><td><input type=text size=9 name=fighters value=\"$row[fighters]\"></td>";
        echo "<td align=right><tt>  Torpedoes  </tt></td><td><input type=text size=9 name=torps value=\"$row[torps]\"></td></tr>";
        echo "<tr><td colspan=2><tt>Planet Production</tt></td>";
        echo "<td align=right><tt>  Organics   </tt></td><td><input type=text size=3 name=prod_organics value=\"$row[prod_organics]\"></td>";
        echo "<td align=right><tt>  Ore        </tt></td><td><input type=text size=3 name=prod_ore value=\"$row[prod_ore]\"></td>";
        echo "<td align=right><tt>  Goods      </tt></td><td><input type=text size=3 name=prod_goods value=\"$row[prod_goods]\"></td>";
        echo "<td align=right><tt>  Energy     </tt></td><td><input type=text size=3 name=prod_energy value=\"$row[prod_energy]\"></td></tr>";
        echo "<tr><td colspan=6><tt>Planet Production</tt></td>";
        echo "<td align=right><tt>  Fighters   </tt></td><td><input type=text size=3 name=prod_fighters value=\"$row[prod_fighters]\"></td>";
        echo "<td align=right><tt>  Torpedoes  </tt></td><td><input type=text size=3 name=prod_torp value=\"$row[prod_torp]\"></td></tr>";
        echo "<tr><td colspan=10>   <hr>       </td></tr>";
        echo "<td align=right><tt>  Computer   </tt></td><td><input type=text size=9 name=computer value=\"$row[computer]\"></td>";
        echo "<td align=right><tt>  Sensors   </tt></td> <td><input type=text size=9 name=sensors value=\"$row[sensors]\"></td>";
        echo "<td align=right><tt>  Beams   </tt></td>   <td><input type=text size=9 name=beams value=\"$row[beams]\"></td>";
        echo "<td align=right><tt>  Torp Launchers   </tt></td><td><input type=text size=9 name=torp_launchers value=\"$row[torp_launchers]\"></td>";
        echo "<tr><td colspan=10>   <hr>       </td></tr>";
        echo "<td align=right><tt>  Shields   </tt></td><td><input type=text size=9 name=shields value=\"$row[shields]\"></td>";
        echo "<td align=right><tt>  armor   </tt></td><td><input type=text size=9 name=armor value=\"$row[armor]\"></td>";
        echo "<td align=right><tt>  armor Points   </tt></td><td><input type=text size=9 name=armor_pts value=\"$row[armor_pts]\"></td>";
        echo "<td align=right><tt>  Cloak   </tt></td><td><input type=text size=9 name=cloak value=\"$row[cloak]\"></td>";
        echo "<tr><td colspan=10>   <hr>       </td></tr>";
        echo "<td align=right><tt>  DELETE     </tt><input type=checkbox name=delete value=ON ></td></tr>";
        echo "</table>";
        echo "<br>";
        echo "<input type=hidden name=planet value=$planet>";
        echo "<input type=hidden name=operation value=save>";
        echo "<input type=submit size=1 value=save ONCLICK=\"clean_js()\">";
    }
    elseif ($_POST['operation'] == "save")
    {
        $_delete = empty($delete) ? "N" : "Y";
        if ($_delete == "Y")
        {
            $debug_query = $db->Execute("DELETE from {$db->prefix}planets where planet_id=?", array($planet));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            echo "<input type=submit value=\"Return to Planet editor\">";
        }
        else
        {
            // update database
            $_defeated = empty($defeated) ? "N" : "Y";
            $_base = empty($base) ? "N" : "Y";
            $_sells = empty($sells) ? "N" : "Y";
            if ($name == "")
            {
                $name = "Unnamed";
            }

            $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET sector_id=?, defeated=?, " .
                                        "name=?, base=?, sells=?, owner=?, " .
                                        "organics=?, ore=?, goods=?, energy=?, " .
                                        "team=?, colonists=?, credits=?, " .
                                        "computer=?, sensors=?, beams=?, " .
                                        "torp_launchers=?, shields=?, armor=?, " .
                                        "armor_pts=?, cloak=?, fighters=?, " .
                                        "torps=?, prod_organics=?, prod_ore=?, " .
                                        "prod_goods=?, prod_energy=?, " .
                                        "prod_fighters=?, prod_torp=? " .
                                        "WHERE planet_id=?", array ($sector_id, $_defeated, $name, $_base, $_sells, $owner, $organics, $ore, $goods, $energy, $team, $colonists, $credits, $computer, $sensors, $beams, $torp_launchers, $shields, $armor, $armor_pts, $cloak, $fighters, $torps, $prod_organics, $prod_ore, $prod_goods, $prod_energy, $prod_fighters, $prod_torp, $planet));
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            // Dynamic functions
            dynamic_loader ($db, "col_count_news.php");
            col_count_news($db, $owner);

            echo "<input type=submit value=\"Return to Planet editor\">";
        }
    }
    else
    {
        echo "Invalid operation";
    }
}

echo "<input type=hidden name=menu value=planedit>";
echo "</form>";
?>
