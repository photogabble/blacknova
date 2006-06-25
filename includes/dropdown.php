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
// File: inclues/dropdown.php

function dropdown($element_name,$current_value, $max_value, $onchange)
{
    // Create dropdowns when called
    $i = $current_value;
    $dropdownvar = "<select size='1' name='$element_name'";
    $dropdownvar = "$dropdownvar $onchange>\n";
    while ($i <= $max_value)
    {
        if ($current_value == $i)
        {
            $dropdownvar = "$dropdownvar        <option value='$i' selected>$i</option>\n";
        }
        else
        {
            $dropdownvar = "$dropdownvar        <option value='$i'>$i</option>\n";
        }

        $i++;
    }

    $dropdownvar = "$dropdownvar       </select>\n";
    return $dropdownvar;
}
?>
