<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: includes/distribute_toll.php

if (preg_match("/distribute_toll.php/i", $_SERVER['PHP_SELF'])) {
      echo "You can not access this file directly!";
      die();
}

function distribute_toll ($db, $dbtables, $sector, $toll, $total_fighters)
{
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' AND defence_type ='F' ");
    echo $db->ErrorMsg();
    // Put the defence information into the array "defenceinfo"
    if ($result3 > 0)
    {
        while (!$result3->EOF)
        {
            $row = $result3->fields;
            $toll_amount = ROUND (($row['quantity'] / $total_fighters) * $toll);
            $db->Execute("UPDATE $dbtables[ships] set credits=credits + $toll_amount WHERE ship_id = $row[ship_id]");
            playerlog ($db, $dbtables, $row['ship_id'], LOG_TOLL_RECV, "$toll_amount|$sector");
            $result3->MoveNext();
        }
    }
}
?>