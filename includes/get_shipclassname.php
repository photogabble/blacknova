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
// File: includes/get_shipclassname.php

function get_shipclassname($db, $ship_class_id)
{
    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    // This was previously cached - consider caching here.
    $res = $db->SelectLimit("SELECT name FROM {$db->prefix}ship_types WHERE type_id=?",1,-1,array($ship_class_id));
    db_op_result($db,$res,__LINE__,__FILE__);

    if ($res)
    {
        return $res->fields['name'];
    }
    else
    {
        return $l_unknown;
    }
}
?>
