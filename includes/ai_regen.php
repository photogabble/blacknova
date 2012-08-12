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
// File: includes/ai_regen.php
//
// Description: The function handling AI regeneration.

$pos = (strpos($_SERVER['PHP_SELF'], "/ai_regen.php"));
if ($pos !== false)
{
    include_once 'global_includes.php';
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    echo $l_cannot_access;
    include_once 'footer.php';
    die();
}

function ai_regen()
{
    // Setup general variables
    global $playerinfo, $fur_unemployment,$ai_isdead;
    global $db;

    dynamic_loader ($db, "playerlog.php");

    //  AI Unempoyment Check
    $playerinfo['credits'] = $playerinfo['credits'] + $fur_unemployment;

    //  Regenerate Energy
    $maxenergy = num_level($playerinfo['power'], $level_factor, $level_magnitude) * 5; // Energy is *5
    if ($playerinfo['ship_energy'] <= ($maxenergy - 50))  //  Stop regen when within 50 of max
    {                                                   //  Regen half of remaining energy
        $playerinfo['ship_energy'] = $playerinfo['ship_energy'] + round(($maxenergy - $playerinfo['ship_energy'])/2);
        $gene = "regenerated Energy to $playerinfo[ship_energy] units,";
    }

    //  Regenerate Armor
    $maxarmor = num_level($playerinfo['armor'], $level_factor, $level_magnitude);
    if ($playerinfo['armor_pts'] <= ($maxarmor - 50))  //  Stop regen when within 50 of max
    {                                                  //  Regen half of remaining armor
        $playerinfo['armor_pts'] = $playerinfo['armor_pts'] + round(($maxarmor - $playerinfo['armor_pts'])/2);
        $gena = "regenerated armor to $playerinfo[armor_pts] points,";
    }

    //  Buy Fighters and Torps
    //  AI Pay 6 per fighter
    $available_fighters = num_level($playerinfo['computer'] $level_factor, $level_magnitude) - $playerinfo['ship_fighters'];
    if (($playerinfo['credits']>5) && ($available_fighters>0))
    {
        if (round($playerinfo['credits']/6)>$available_fighters)
        {
            $purchase = ($available_fighters*6);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $available_fighters;
            $genf = "purchased $available_fighters fighters for $purchase credits,";
        }

        if (round($playerinfo['credits']/6)<= $available_fighters)
        {
            $purchase = (round($playerinfo['credits']/6));
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $purchase;
            $genf = "purchased $purchase fighters for $playerinfo[credits] credits,";
            $playerinfo['credits'] = 0;
        }
    }

    //  AI pay 3 per torpedo
    $available_torpedoes = num_level($playerinfo['torp_launchers'], $level_factor, $level_magnitude) - $playerinfo['torps'];
    if (($playerinfo['credits']>2) && ($available_torpedoes>0))
    {
        if (round($playerinfo['credits']/3)>$available_torpedoes)
        {
            $purchase = ($available_torpedoes*3);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['torps'] = $playerinfo['torps'] + $available_torpedoes;
            $gent = "purchased $available_torpedoes torpedoes for $purchase credits,";
        }

        if (round($playerinfo['credits']/3)<= $available_torpedoes)
        {
            $purchase = (round($playerinfo['credits']/3));
            $playerinfo['torps'] = $playerinfo['torps'] + $purchase;
            $gent = "purchased $purchase torpedoes for $playerinfo[credits] credits,";
            $playerinfo['credits'] = 0;
        }
    }

    //  Update AI Record
    $db->Execute ("UPDATE {$db->prefix}ships SET ship_energy=?, armor_pts=?, ship_fighters=?, torps=?, credits=? " .
                  "WHERE ship_id=?", array($playerinfo['ship_energy'], $playerinfo['armor_pts'], $playerinfo['ship_fighters'], $playerinfo['torps'], $playerinfo['credits'], $playerinfo['ship_id']));
    if (!$gene=='' || !$gena=='' || !$genf=='' || !$gent=='')
    {
        playerlog($db,$playerinfo['ship_id'], "LOG_RAW", "kabal $gene $gena $genf $gent and has been updated.");
    }
}
?>
