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
// File: log_parse.php
function log_parse($entry, $l_log_pod, $l_log_nopod, $space_plague_kills)
{
    $texttemp = "l_log_text_" . $entry['type'];
    $titletemp = "l_log_title_" . $entry['type'];
    global $$texttemp, $$titletemp;

    switch ($entry['type'])
    {
        case "LOG_LOGIN": // Data args are [ip]
        case "LOG_LOGOUT":
        case "LOG_BADLOGIN":
        case "LOG_HARAKIRI":
        $retvalue['text'] = str_replace("[ip]", "<font color=white><strong>$entry[log_data]</strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_ATTACK_OUTMAN": // Data args are [player]
        case "LOG_ATTACK_OUTSCAN":
        case "LOG_ATTACK_EWD":
        case "LOG_ATTACK_EWDFAIL":
        case "LOG_SHIP_SCAN":
        case "LOG_SHIP_SCAN_FAIL":
        case "LOG_AI_ATTACK":
        case "LOG_TEAM_NOT_LEAVE":
        case "LOG_DEFEND_WIN":
        case "LOG_DEFEND_WIN_POD":
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$entry[log_data]</strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_ATTACK_LOSE": // Data args are [player] [pod]
        list($name, $pod)= split ("\|", $entry['log_data']);

        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        if ($pod == 'Y')
        {
            $retvalue['text'] = $retvalue['text'] . $l_log_pod;
        }
        else
        {
            $retvalue['text'] = $retvalue['text'] . "<font color=\"yellow\"><strong>" .$l_log_nopod . "</strong></font>";
        }
        break;

        case "LOG_ATTACKED_WIN": // Data args are [player] [armor] [fighters]
        list($name, $armor, $fighters)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[armor]", "<font color=white><strong>$armor</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[fighters]", "<font color=white><strong>$fighters</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_TOLL_PAID": // Data args are [toll] [sector]
        case "LOG_TOLL_RECV":
        list($toll, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[toll]", "<font color=white><strong>$toll</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_HIT_MINES": // Data args are [mines] [sector]
        list($mines, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[mines]", "<font color=white><strong>$mines</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SHIP_DESTROYED_MINES": // Data args are [sector] [pod]
        case "LOG_DEFS_KABOOM":
        list($sector, $pod)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        if ($pod == 'Y')
        {
            $retvalue['text'] = $retvalue['text'] . $l_log_pod;
        }
        else
        {
            $retvalue['text'] = $retvalue['text'] . "<font color=\"yellow\"><strong>" . $l_log_nopod . "</strong></font>";
        }
        break;

        case "LOG_PLANET_DEFEATED_D": // Data args are :[planet_name] [sector] [name]
        case "LOG_PLANET_DEFEATED":
        case "LOG_PLANET_SCAN":
        case "LOG_PLANET_SCAN_FAIL":
        case "LOG_PLANET_YOUR_CAPTURED":
        case "LOG_SPY_SEND_FAIL":
        case "LOG_SPY_CPTURE_OWNER":
        case "LOG_SPY_KILLED":
        case "LOG_SHIP_KILLED_BY_PLANET":
        list($planet_name, $sector, $name)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_PLANET_NOT_DEFEATED": // Data args are [planet_name] [sector] [name] [ore] [organics] [goods] [salvage] [credits]
        list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[ore]", "<font color=white><strong>$ore</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[goods]", "<font color=white><strong>$goods</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[organics]", "<font color=white><strong>$organics</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[salvage]", "<font color=white><strong>$salvage</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[credits]", "<font color=white><strong>$credits</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_RAW": // Data is stored as a message
        $retvalue['title'] = $$titletemp;
        $retvalue['text'] = $entry['log_data'];
        break;

        case "LOG_DEFS_DESTROYED": // Data args are [quantity] [type] [sector]
        list($quantity, $type, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[quantity]", "<font color=white><strong>$quantity</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[type]", "<font color=white><strong>$type</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_PLANET_EJECT": // Data args are [sector] [player]
        list($sector, $name)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_STARVATION": // Data args are [sector] [starvation]
        list($sector, $starvation)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[starvation]", "<font color=white><strong>$starvation</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_TOW": // Data args are [sector] [newsector] [hull]
        list($sector, $newsector, $hull)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[newsector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$newsector>$newsector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[hull]", "<font color=white><strong>$hull</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_DEFS_DESTROYED_F": // Data args are [fighters] [sector]
        list($fighters, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[fighters]", "<font color=white><strong>$fighters</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_TEAM_REJECT": // Data args are [player] [teamname]
        list($player, $teamname)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$player</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[teamname]", "<font color=white><strong>$teamname</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_TEAM_RENAME": // Data args are [team]
        case "LOG_TEAM_M_RENAME":
        case "LOG_TEAM_KICK":
        case "LOG_TEAM_CREATE":
        case "LOG_TEAM_LEAVE":
        case "LOG_TEAM_LEAD":
        case "LOG_TEAM_JOIN":
        case "LOG_TEAM_INVITE":
        $retvalue['text'] = str_replace("[team]", "<font color=white><strong>$entry[log_data]</strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_TEAM_NEWLEAD": // Data args are [team] [name]
        case "LOG_TEAM_NEWMEMBER":
        list($team, $name)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[team]", "<font color=white><strong>$team</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_ADMIN_HARAKIRI": // Data args are [player] [ip]
        list($player, $ip_address)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$player</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[ip]", "<font color=white><strong>$ip_address</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_ADMIN_ILLEGVALUE": // Data args are [player] [quantity] [type] [holds]
        list($player, $quantity, $type, $holds)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$player</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[quantity]", "<font color=white><strong>$quantity</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[type]", "<font color=white><strong>$type</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[holds]", "<font color=white><strong>$holds</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_ADMIN_PLANETDEL": // Data args are [attacker] [defender] [sector]
        list($attacker, $defender, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[attacker]", "<font color=white><strong>$attacker</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[defender]", "<font color=white><strong>$defender</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_DEFENSE_DEGRADE": // Data args are [sector] [degrade]
        list($sector, $degrade)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[degrade]", "<font color=white><strong>$degrade</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_PLANET_CAPTURED": // Data args are [cols] [credits] [owner]
        list($cols, $credits, $owner)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[cols]", "<font color=white><strong>$cols</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[credits]", "<font color=white><strong>$credits</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[owner]", "<font color=white><strong>$owner</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_BOUNTY_CLAIMED":
        list($amount,$bounty_on,$placed_by) = split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[amount]", "<font color=white><strong>$amount</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><strong>$bounty_on</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[placed_by]", "<font color=white><strong>$placed_by</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_BOUNTY_PAID":
        list($amount,$bounty_on) = split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[amount]", "<font color=white><strong>$amount</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><strong>$bounty_on</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_BOUNTY_CANCELLED":
        list($amount,$bounty_on) = split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[amount]", "<font color=white><strong>$amount</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[bounty_on]", "<font color=white><strong>$bounty_on</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_BOUNTY_FEDBOUNTY":
        $retvalue['text'] = str_replace("[amount]", "<font color=white><strong>$entry[log_data]</strong></font>", $$texttemp);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SPACE_PLAGUE":
        list($name,$sector) = split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $percentage = $space_plague_kills * 100;
        $retvalue['text'] = str_replace("[percentage]", "$percentage", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_PLASMA_STORM":
        list($name,$sector,$percentage) = split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[percentage]", "<font color=white><strong>$percentage</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_PLANET_BOMBED":
        list($planet_name, $sector, $name, $beams, $torps, $figs)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[beams]", "<font color=white><strong>$beams</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[torps]", "<font color=white><strong>$torps</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[figs]", "<font color=white><strong>$figs</strong></font>", $retvalue['text']);
        $retvalue['title'] = "<font color=\"red\">" . $$titletemp . "</font>";
        break;

        case "LOG_CHEAT_TEAM": // Data args are [player] [ip]
        list($name, $ip_address)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[player]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[ip]", "<font color=white><strong>$ip_address</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SPY_SABOTAGE": // Data args are :[id] [planet_name] [sector] [log_data]
        case "LOG_SPY_BIRTH":
        case "LOG_SPY_INTEREST":
        case "LOG_SPY_MONEY":
        case "LOG_SPY_TORPS":
        case "LOG_SPY_FITS":
        list($id, $planet_name, $sector, $log_data)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[id]", "<font color=white><strong>$id</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[log_data]", "<font color=white><strong>$log_data</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SPY_CPTURE": // Data args are :[id] [planet_name] [sector]
        case "LOG_SPY_KILLED_SPYOWNER":
        case "LOG_SPY_CATACLYSM":
        list($id, $planet_name, $sector)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[id]", "<font color=white><strong>$id</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SHIPSPY_KILLED": // Data args are :[id] [name] [shipname]
        case "LOG_SHIPSPY_CATACLYSM":
        case "LOG_SPY_NEWSHIP":
        list($id, $name, $shipname)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[shipname]", "<font color=white><strong>$shipname</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[id]", "<font color=white><strong>$id</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_SPY_TOSHIP": // Data args are :[id] [planet_name] [sector] [playername] [shipname]
        case "LOG_SPY_TOPLANET":
        list($id, $planet_name, $sector, $playername, $shipname)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[planet_name]", "<font color=white><strong>$planet_name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sector]", "<font color=white><strong><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[id]", "<font color=white><strong>$id</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[playername]", "<font color=white><strong>$playername</strong></font>", $retvalue['text']);
        $retvalue['text'] = str_replace("[shipname]", "<font color=white><strong>$shipname</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;

        case "LOG_IGB_TRANSFER1": // Data args are [name] [sum]
        case "LOG_IGB_TRANSFER2":
        list($name, $sum)= split ("\|", $entry['log_data']);
        $retvalue['text'] = str_replace("[name]", "<font color=white><strong>$name</strong></font>", $$texttemp);
        $retvalue['text'] = str_replace("[sum]", "<font color=white><strong>$sum</strong></font>", $retvalue['text']);
        $retvalue['title'] = $$titletemp;
        break;
    }

    if (!isset($retvalue))
    {
//        var_dump($entry);
    }
    return $retvalue;
}
?>
