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
// File: includes/traderoute_check_compatible.php

function traderoute_check_compatible($type1, $type2, $move, $circuit, $src, $dest)
{
    global $playerinfo;
    global $l_tdr_nowlink1, $l_tdr_nowlink2, $l_tdr_sportissrc, $l_tdr_notownplanet, $l_tdr_planetisdest;
    global $l_tdr_samecom, $l_tdr_sportcom, $l_tdr_invalidspoint;
    global $db;

    // Check warp links compatibility
    if ($move == 'warp')
    {
        $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array($src['sector_id'], $dest['sector_id']));
        if ($query->EOF)
        {
            $l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $src['sector_id'], $l_tdr_nowlink1);
            $l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
            traderoute_die($l_tdr_nowlink1);
        }

        if ($circuit == '2')
        {
            $query = $db->Execute("SELECT link_id FROM {$db->prefix}links WHERE link_start=? AND link_dest=?", array($dest['sector_id'], $src['sector_id']));
            if ($query->EOF)
            {
                $l_tdr_nowlink2 = str_replace("[tdr_src_sector_id]", $src['sector_id'], $l_tdr_nowlink2);
                $l_tdr_nowlink2 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink2);
                traderoute_die($l_tdr_nowlink2);
            }
        }
    }

    // Check ports compatibility
    if ($type1 == 'port')
    {
        if ($src['port_type'] == 'upgrades')
        {
            if (($type2 != 'planet') && ($type2 != 'team_planet'))
            {
                traderoute_die($l_tdr_sportissrc);
            }

            if ($dest['owner'] != $playerinfo['player_id'] && ($dest['team'] == 0 || ($dest['team'] != $playerinfo['team'])))
            {
                traderoute_die($l_tdr_notownplanet);
            }
        }
        else
        {
            if ($type2 == 'planet')
            {
                traderoute_die($l_tdr_planetisdest);
            }

            if ($type2 != 'planet')
            {
                if ($src['port_type'] == $dest['port_type'])
                {
                    traderoute_die($l_tdr_samecom);
                }
            }
        }

        if ($src['port_type'] == 'devices')
        {
            traderoute_die($l_tdr_invalidspoint);
        }
    }
    else
    {
        if ($dest['port_type'] == 'upgrades')
        {
            traderoute_die($l_tdr_sportcom);
        }
    }
}
?>
