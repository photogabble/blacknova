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
// File: message_defense_owner.php
function message_defense_owner($db, $sector, $message)
{
    dynamic_loader ($db, "playerlog.php");

    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id='$sector' ");
    db_op_result($db,$result3,__LINE__,__FILE__);

    // Put the defense information into the array "defenseinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            playerlog($db,$result3->fields['player_id'], "LOG_RAW", $message);
            $result3->MoveNext();
        }
    }
}
?>
