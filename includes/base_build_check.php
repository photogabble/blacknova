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
// File: includes/base_build_check.php

function base_build_check($planet, $i)
{
    global $l_yes, $l_no;
    global $base_ore, $base_organics, $base_goods, $base_credits, $l_pr_build;

    if ($planet[$i]['base'] == 'Y')
    {
        return $l_yes;
    }
    elseif ($planet[$i]['ore'] >= $base_ore && $planet[$i]['organics'] >= $base_organics && $planet[$i]['goods'] >= $base_goods && $planet[$i]['credits'] >=$base_credits)
    {
        return "<a href=\"planet_report_ce.php?buildp=" . $planet[$i]['planet_id'] . "&builds=" . $planet[$i]['sector_id'] . "\">$l_pr_build</a>";
    }
    else
    {
        return $l_no;
    }
}
?>
