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
// File: includes/dbtm2unixtime

function dbtm2unixtime( $dbtm2timestamp_in )
{
   // Returns unix time stamp for a given date time string that comes from DB
   list( $date, $time ) = split(" ", $dbtm2timestamp_in);
   list( $year, $month, $day ) = split( "-", $date );
   list( $hour, $minute, $second ) = split( ":", $time );

   return mktime( $hour, $minute, $second, $month, $day, $year );
}
?>
