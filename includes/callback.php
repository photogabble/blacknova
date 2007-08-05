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
// File: includes/callback.php

function callback($buffer)
{
    // This sends the entire buffer of the page through utf8 encoding, to ensure
    // that all output is encoded using utf8 characters (four bytes)
    // Doing so allows xml output, and consistent utf8 output for all pages.

    // utf8_encode is actually meant to convert from ISO8859-1 to utf8 ONLY. Since
    // we don't receive that data, or produce that data, we don't need this.
    return $buffer;
//    return utf8_encode($buffer);
}
?>
