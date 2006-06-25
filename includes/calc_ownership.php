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
// File: includes/calc_ownership.php

function calc_ownership($db, $sector)
{
    // Dynamic functions
    dynamic_loader ($db, "calc_ownership.php");

    global $min_bases_to_own;
    global $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
    global $l_global_nochange;

    $res = $db->Execute("SELECT owner, team FROM {$db->prefix}planets WHERE sector_id=? AND base='Y'", array($sector));
    db_op_result($db,$res,__LINE__,__FILE__);

    $num_bases = $res->RecordCount();
    $i = 0;
    if ($num_bases > 0)
    {
        while (!$res->EOF)
        {
            $bases[$i] = $res->fields;
            $i++;
            $res->MoveNext();
        }
    }
    else
    {
//        $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id='$sector'");
//        $sectorinfo = $result->fields;
//        if ($sectorinfo['zone_id'] > 2) // 1 is unowned, so we dont need to redo it. 2 is fed space, and protected.
//        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=1 WHERE sector_id=? AND zone_id > '2' ", array($sector));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
//        }
            return $l_global_nzone;
    }

    $owner_num = 0;
    foreach ($bases as $curbase)
    {
        $curteam = -1;
        $curship = -1;
        $loop = 0;
        while ($loop < $owner_num)
        {
            if ($curbase['team'] != 0)
            {
                if ($owners[$loop]['type'] == 'C')
                {
                    if ($owners[$loop]['id'] == $curbase['team'])
                    {
                        $curteam = $loop;
                        $owners[$loop]['num']++;
                    }
                }
            }

            if ($owners[$loop]['type'] == 'S')
            {
                if ($owners[$loop]['id'] == $curbase['owner'])
                {
                    $curship=$loop;
                    $owners[$loop]['num']++;
                }
            }
            $loop++;
        }

        if ($curteam == -1)
        {
            if ($curbase['team'] != 0)
            {
                $curteam = $owner_num;
                $owner_num++;
                $owners[$curteam]['type'] = 'C';
                $owners[$curteam]['num'] = 1;
                $owners[$curteam]['id'] = $curbase['team'];
            }
        }

        if ($curship == -1)
        {
            if ($curbase['owner'] != 0)
            {
                $curship = $owner_num;
                $owner_num++;
                $owners[$curship]['type'] = 'S';
                $owners[$curship]['num'] = 1;
                $owners[$curship]['id'] = $curbase['owner'];
            }
        }
    }

    // We've got all the contenders with their bases.
    // Time to test for conflict

    $loop = 0;
    $nbteams = 0;
    $nbships = 0;

    while ($loop < $owner_num)
    {
        if ($owners[$loop]['type'] == 'C')
        {
            $nbteams++;
        }
        else
        {
            $res = $db->Execute("SELECT team FROM {$db->prefix}players WHERE player_id=?", array($owners[$loop]['id']));
            db_op_result($db,$res,__LINE__,__FILE__);
            if ($res && $res->RecordCount() != 0)
            {
                $curship = $res->fields;
                $ships[$nbships]=$owners[$loop]['id'];
                $steams[$nbships]=$curship['team'];
                $nbships++;
            }
        }
        $loop++;
    }

    // More than one team, war
    if ($nbteams > 1)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=4 WHERE sector_id=?", array($sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        return $l_global_warzone;
    }

    // More than one unallied ship, war
    $numunallied = 0;
    foreach ($steams as $team)
    {
        if ($team == 0)
        {
            $numunallied++;
        }
    }

    if ($numunallied > 1)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=4 WHERE sector_id=?", array($sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        return $l_global_warzone;
    }

    // Unallied ship, another team present, war
    if ($numunallied > 0 && $nbteams > 0)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=4 WHERE sector_id=?", array($sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        return $l_global_warzone;
    }

    // Unallied ship, another ship in a team, war
    if ($numunallied > 0)
    {
        $query = "SELECT team FROM {$db->prefix}players WHERE (";
        $i = 0;
        foreach ($ships as $ship)
        {
            $query = $query . "player_id=$ship";
            $i++;

            if ($i != $nbships)
            {
                $query = $query . " OR ";
            }
            else
            {
                $query = $query . ")";
            }
        }

        $query = $query . " AND team!=0";
        $res = $db->Execute($query);
        db_op_result($db,$res,__LINE__,__FILE__);

        if ($res->RecordCount() != 0)
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=4 WHERE sector_id=?", array($sector));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            return $l_global_warzone;
        }
    }

    // Ok, all bases are allied at this point. Let's make a winner.
    $winner = 0;
    $i = 1;
    while ($i < $owner_num)
    {
        if ($owners[$i]['num'] > $owners[$winner]['num'])
        {
            $winner = $i;
        }
        elseif ($owners[$i]['num'] == $owners[$winner]['num'])
        {
            if ($owners[$i]['type'] == 'C')
            {
                $winner = $i;
            }
        }
        $i++;
    }

    $res = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE sector_id=?", array($sector));
    db_op_result($db,$res,__LINE__,__FILE__);
    $num_planets = $res->RecordCount();

    $min_bases_to_own = round(($num_planets+1)/2);

    if ($owners[$winner]['num'] < $min_bases_to_own)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=1 WHERE sector_id=?", array($sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        return $l_global_nzone;
    }

    if ($owners[$winner]['type'] == 'C')
    {
        $res = $db->Execute("SELECT zone_id FROM {$db->prefix}zones WHERE team_zone='Y' && owner=?", array($owners[$winner]['id']));
        db_op_result($db,$res,__LINE__,__FILE__);
        $zone = $res->fields;

        $res = $db->Execute("SELECT team_name FROM {$db->prefix}teams WHERE team_id=?", array($owners[$winner]['id']));
        db_op_result($db,$res,__LINE__,__FILE__);
        $team = $res->fields;

        $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=? WHERE sector_id=?", array($zone['zone_id'], $sector));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        return "$l_global_team $team[team_name]!";
    }
    else
    {
        $onpar = 0;
        foreach ($owners as $curowner)
        {
            if ($curowner['type'] == 'S' && $curowner['id'] != $owners[$winner]['id'] && $curowner['num'] == $owners[winner]['num'])
            $onpar = 1;
            break;
        }

        // Two allies have the same number of bases
        if ($onpar == 1)
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=1 WHERE sector_id=?", array($sector));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            return $l_global_nzone;
        }
        else
        {
            $res = $db->Execute("SELECT zone_id FROM {$db->prefix}zones WHERE team_zone='N' and owner=?", array($owners[$winner]['id']));
            db_op_result($db,$res,__LINE__,__FILE__);
            $zone = $res->fields;

            $res = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($owners[$winner]['id']));
            db_op_result($db,$res,__LINE__,__FILE__);
            $ship = $res->fields;

            $debug_query = $db->Execute("UPDATE {$db->prefix}universe SET zone_id=? WHERE sector_id=?", array($zone['zone_id'], $sector));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            return "$l_global_player $ship[character_name]!";
        }
    }
}
?>
