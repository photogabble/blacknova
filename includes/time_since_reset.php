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
// File: time_since_reset.php

function time_since_reset($db)
{
    $debug_query = $db->SelectLimit("SELECT date FROM {$db->prefix}news",1);
    db_op_result($db,$debug_query,__LINE__,__FILE__);
    $throwaway = $debug_query->fields['date'];
    $creation_date = $db->UnixTimeStamp($throwaway);

    // Select creation news (should always be the first event)
    $time_since = time() - $creation_date;
    $timestring = '';

    $weeks = floor($time_since/604800);
    $days = floor(($time_since%604800)/86400);
    $hours = floor((($time_since%604800)%86400)/3600);
    $minutes = floor(((($time_since%604800)%86400)%3600)/60);

    if ($weeks)
    {
        $timestring=$weeks." weeks ";
    }

    if ($days)
    {
        $timestring.=$days." days ";
    }

    if ($hours)
    {
        $timestring.=$hours." hours ";
    }

    if ($minutes)
    {
        $timestring.=$minutes." minutes";
    }

    return $timestring;
}
?>
