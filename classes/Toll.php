<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
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
// File: classes/Toll.php

namespace Bnt;

class Toll
{
    public static function distribute($db, $sector, $toll, $total_fighters)
    {
        $select_def_res = $db->Execute("SELECT * FROM {$db->prefix}sector_defence WHERE sector_id=? AND defence_type ='F'", array($sector));
        Db::logDbErrors($db, $select_def_res, __LINE__, __FILE__);

        // Put the defence information into the array "defenceinfo"
        if ($select_def_res instanceof ADORecordSet)
        {
            while (!$select_def_res->EOF)
            {
                $row = $select_def_res->fields;
                $toll_amount = round(($row['quantity'] / $total_fighters) * $toll);
                $res = $db->Execute("UPDATE {$db->prefix}ships SET credits=credits + ? WHERE ship_id = ?", array($toll_amount, $row['ship_id']));
                Db::logDbErrors($db, $res, __LINE__, __FILE__);
                PlayerLog::writeLog($db, $row['ship_id'], LOG_TOLL_RECV, "$toll_amount|$sector");
                $select_def_res->MoveNext();
            }
        }
    }
}
?>
