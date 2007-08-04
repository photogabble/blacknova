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
// File: includes/playerdeath.php

function playerdeath($db,$corpse_id, $logtype, $loginfo=0, $murdered=0, $murderer_id=0, $ship_id=0)
{
    global $shipinfo, $playerinfo;
    global $start_armor, $start_energy, $start_fighters, $spy_success_factor;
    global $l_chm_luckescapepod;

    dynamic_loader ($db, "playerlog.php");

    $debug_query = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=$ship_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $corpse_shipinfo = $debug_query->fields;

    if (($logtype == LOG_ATTACK_LOSE) && ($corpse_shipinfo['dev_escapepod'] == 'Y'))
    {
        playerlog($db,$corpse_id, "LOG_ATTACK_LOSE", $playerinfo['character_name']."|Y");
    }
    elseif (($logtype == LOG_ATTACK_LOSE) && ($corpse_shipinfo['dev_escapepod'] != 'Y'))
    {
        playerlog($db,$corpse_id, "LOG_ATTACK_LOSE", $playerinfo['character_name']. "|N");
    }
    elseif (($logtype == LOG_DEFEND) && ($corpse_shipinfo['dev_escapepod'] == 'Y'))
    {
        playerlog($db,$corpse_id, "LOG_DEFEND_WIN_POD", $loginfo);
    }
    elseif (($logtype == LOG_DEFEND) && ($corpse_shipinfo['dev_escapepod'] != 'Y'))
    {
        playerlog($db,$corpse_id, "LOG_DEFEND_WIN", $loginfo);
    }
    elseif ($logtype == LOG_HARAKIRI)
    {
        global $playerinfo;
        adminlog($db, "LOG_ADMIN_HARAKIRI", "$playerinfo[character_name]|$_SESSION[ip_address]");
        playerlog($db,$corpse_id, "LOG_HARAKIRI", "$_SESSION[ip_address]");
    }
    else
    {
        playerlog($db,$corpse_id, $logtype, $loginfo);
    }

    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=0 WHERE player_id=$corpse_id");
    db_op_result($db,$debug_query,__LINE__,__FILE__);

    if ($corpse_shipinfo['dev_escapepod'] == "Y")
    {
        $rating = round($playerinfo['rating']/2);
        echo $l_chm_luckescapepod. "<br><br>";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET class=1, hull=0, engines=0, pengines=0, power=0, sensors=0, " .
                                    "computer=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=$start_armor, " .
                                    "cloak=0, shields=0, sector_id=1, organics=0, ore=0, goods=0, energy=$start_energy, " .
                                    "colonists=0, fighters=$start_fighters, dev_warpedit=0, dev_genesis=0, dev_emerwarp=0, " .    
                                    "dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, on_planet='N', " .
                                    "cleared_defenses=' ' WHERE ship_id=$ship_id");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        if ($spy_success_factor)
        {
            dynamic_loader ($db, "spy_ship_destroyed.php");
            spy_ship_destroyed($db,$ship_id,$murderer_id);
        }

    }
    else
    {
        db_kill_player($db, $corpse_id);
    }

    if ($murdered !=0)
    {
        cancel_bounty($db, $corpse_id);
    }
    else
    {
        collect_bounty($db, $murderer_id, $corpse_id);
    }

}
?>
