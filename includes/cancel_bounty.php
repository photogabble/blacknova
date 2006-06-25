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
// File: inclues/cancel_bounty.php

function cancel_bounty($db, $bounty_on)
{
    dynamic_loader ($db, "playerlog.php");

    $res = $db->Execute("SELECT * FROM {$db->prefix}bounty, {$db->prefix}players WHERE bounty_on=? AND bounty_on = player_id", array($bounty_on));
    db_op_result($db,$res,__LINE__,__FILE__);
    if ($res)
    {
        while (!$res->EOF)
        {
            $bountydetails = $res->fields;
            if ($bountydetails['placed_by'] != 0)
            {
                $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits = credits + ? WHERE player_id=?", array($bountydetails['character_name'], $bountydetails['placed_by']));
                db_op_result($db,$debug_query,__LINE__,__FILE__);
                playerlog($db,$bountydetails['placed_by'], "LOG_BOUNTY_CANCELLED","$bountydetails[amount]|$bountydetails[character_name]");
            }

            $debug_query = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE bounty_id=?", array($bountydetails['bounty_id']));
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            $res->MoveNext();
        }
    }
}
?>
