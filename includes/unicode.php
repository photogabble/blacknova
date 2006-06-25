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
// File: includes/unicode.php
//
// These functions are for future use in game.
// Begin unicde functions.
// These ensure that once we complete support for it, the game admin will be able to run a game that accepts multi-byte input safely (like Japanese).
function utf8_to_unicode($str)
{
    $unicode = array();
    $values = array();
    $lookingFor = 1;
        
    for ($i = 0; $i < strlen($str); $i++)
    {
        $thisValue = ord($str[$i]);

        if ($thisValue < 128)
        {
            $unicode[] = $thisValue;
        }
        else
        {
            if (count($values) == 0) 
            {
                $lookingFor = ($thisValue < 224) ? 2 : 3;
            }
                
            $values[] = $thisValue;
                
            if (count($values) == $lookingFor)
            {
                $number = ( $lookingFor == 3 ) ?
                          ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                          ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
                        
                $unicode[] = $number;
                $values = array();
                $lookingFor = 1;
            } // if
        } // if/else
    } // for

    return $unicode;
}

function strpos_unicode($haystack , $needle , $offset = 0)
{
    $position = $offset;
    $found = FALSE;

    while((!$found) && ($position < count($haystack)))
    {
        if ($needle[0] == $haystack[$position])
        {
            for ($i = 1; $i < count($needle); $i++)
            {
                if ($needle[$i] != $haystack[$position + $i])
                {
                    break;
                }
            }
                
            if ($i == count($needle))
            {
                $found = TRUE;
                $position--;
            }
        }

        $position++;
    }

    return ($found == TRUE) ? $position : FALSE;
}

function unicode_to_entities($unicode)
{
    $entities = '';
    foreach($unicode as $value) $entities .= '&#' . $value . ';';
    return $entities;
}

function unicode_to_entities_preserving_ascii($unicode)
{
    $entities = '';
    foreach ($unicode as $value)
    {
        $entities .= ($value > 127) ? '&#' . $value . ';' : chr($value);
    }

    return $entities;
}

function unicode_to_utf8($str)
{
    $utf8 = '';
    foreach($str as $unicode)
    {
        if ($unicode < 128)
        {
            $utf8.= chr($unicode);
        }
        elseif ($unicode < 2048)
        {
            $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );
        }
        else
        {
            $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
            $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );
        }
    }

    return $utf8;
}
?>
