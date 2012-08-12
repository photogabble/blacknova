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
// File: includes/spy_planet_destroyed.php

function spy_planet_destroyed($db,$planet_id)
{
    dynamic_loader ($db, "playerlog.php");

    $res = $db->Execute("SELECT {$db->prefix}spies.*, {$db->prefix}planets.name, {$db->prefix}planets.sector_id FROM {$db->prefix}spies INNER JOIN {$db->prefix}planets ON {$db->prefix}spies.planet_id = {$db->prefix}planets.planet_id WHERE {$db->prefix}spies.planet_id=?", array($planet_id));
    while (!$res->EOF)
    {
        $owners = $res->fields;
        playerlog($db,$owners[owner_id], "LOG_SPY_CATACLYSM", "$owners[spy_id]|$owners[name]|$owners[sector_id]");
        $res->MoveNext();
    }

    $db->Execute("DELETE FROM {$db->prefix}spies WHERE planet_id=?", array($planet_id));
}
?>
