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
// File: includes/change_planet_ownership.php

function change_planet_ownership($db, $planet_id, $old_owner, $new_owner = 0)
{
    // Dynamic functions
    dynamic_loader ($db, "gen_score.php");

    if ($new_owner)
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET active='N', job_id='0', spy_percent='0.0' WHERE planet_id=? AND owner_id=?", array($planet_id, $new_owner));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET active='Y' WHERE planet_id=? AND owner_id!=?", array($planet_id,$new_owner));
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        gen_score($db,$new_owner);
    }
    else
    {
        $debug_query = $db->Execute("UPDATE {$db->prefix}spies SET active='N', job_id='0', spy_percent='0.0' WHERE planet_id=?", array($planet_id));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    }

    if ($old_owner)
    {
        gen_score($db,$old_owner);
    }
}
?>
