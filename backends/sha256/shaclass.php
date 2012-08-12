<?php

/*******************************************************************************
 *
 *    SHA256 static class for PHP4
 *    implemented by feyd _at_ devnetwork .dot. net
 *    specification from http://csrc.nist.gov/cryptval/shs/sha256-384-512.pdf
 *
 *    (C) Copyright 2005 Developer's Network. All rights reserved.
 *
 *    This library is free software; you can redistribute it and/or modify it
 *    under the terms of the GNU Lesser General Public License as published by the
 *    Free Software Foundation; either version 2.1 of the License, or (at your
 *    option) any later version.
 *
 *    This library is distributed in the hope that it will be useful, but WITHOUT
 *    ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *    FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 *    for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with this library; if not, write to the
 *        Free Software Foundation, Inc.
 *        59 Temple Place, Suite 330,
 *        Boston, MA 02111-1307 USA
 *
 *    Thanks to CertainKey Inc. for providing some example outputs in Javascript
 *
 ******************************************************************************/

 /*==============================================================================
 *    SHA256 Main Class
 *============================================================================*/

class SHA256
{
    // hash a known string of data
    public function hash($str, $mode = 'hex')
    {
        return SHA256::_hash( '', $str, $mode );
    }

    //    hash a file
    public function hashFile($filename, $mode = 'hex')
    {
        return SHA256::_hash( 'File', $filename, $mode );
    }

    //    hash a URL
    public function hashURL($url, $mode = 'hex')
    {
        return SHA256::_hash( 'URL', $url, $mode );
    }


    //    -------------------------------
    //    BEGIN INTERNAL FUNCTIONS
    //    -------------------------------

    //    the actual hash interface function, which ~dynamically switches types.
    public function _hash( $type, $str, $mode )
    {
        $modes = array( 'hex', 'bin', 'bit' );
        $ret = false;

        if(!in_array(strtolower($mode), $modes))
        {
            trigger_error('mode specified is unrecognized: ' . $mode, E_USER_WARNING);
        }
        else
        {
            $data =& new SHA256Data( $type, $str );

            SHA256::compute($data);

            $func = array('SHA256', 'hash' . $mode);
            if(is_callable($func))
            {
                $func = 'hash' . $mode;
                $ret = SHA256::$func($data);
                //$ret = call_user_func($func, $data);

                if( $mode === 'HEX' )
                {
                    $ret = strtoupper( $ret );
                }
            }
            else
            {
                trigger_error('SHA256::hash' . $mode . '() NOT IMPLEMENTED.', E_USER_WARNING);
            }
        }

        return $ret;
    }


    //    32-bit summation
    public function sum()
    {
        $T = 0;
        for($x = 0, $y = func_num_args(); $x < $y; $x++)
        {
            //    argument
            $a = func_get_arg($x);

            //    carry storage
            $c = 0;

            for($i = 0; $i < 32; $i++)
            {
                //    sum of the bits at $i
                $j = (($T >> $i) & 1) + (($a >> $i) & 1) + $c;
                //    carry of the bits at $i
                $c = ($j >> 1) & 1;
                //    strip the carry
                $j &= 1;
                //    clear the bit
                $T &= ~(1 << $i);
                //    set the bit
                $T |= $j << $i;
            }
        }

        return $T;
    }


    //    compute the hash. This is the real hashing function.
    public function compute(&$hashData)
    {
        static $vars = 'abcdefgh';
        static $K = null;

        if($K === null)
        {
            $K = array (
                 1116352408,     1899447441,    -1245643825,     -373957723,
                  961987163,     1508970993,    -1841331548,    -1424204075,
                 -670586216,      310598401,      607225278,     1426881987,
                 1925078388,    -2132889090,    -1680079193,    -1046744716,
                 -459576895,     -272742522,      264347078,      604807628,
                  770255983,     1249150122,     1555081692,     1996064986,
                -1740746414,    -1473132947,    -1341970488,    -1084653625,
                 -958395405,     -710438585,      113926993,      338241895,
                  666307205,      773529912,     1294757372,     1396182291,
                 1695183700,     1986661051,    -2117940946,    -1838011259,
                -1564481375,    -1474664885,    -1035236496,     -949202525,
                 -778901479,     -694614492,     -200395387,      275423344,
                  430227734,      506948616,      659060556,      883997877,
                  958139571,     1322822218,     1537002063,     1747873779,
                 1955562222,     2024104815,    -2067236844,    -1933114872,
                -1866530822,    -1538233109,    -1090935817,     -965641998,
                );
        }

        $W = array();
        while(($chunk = $hashData->message->nextChunk()) !== false)
        {
            //    initialize the registers
            for($j = 0; $j < 8; $j++)
                ${$vars{$j}} = $hashData->hash[$j];

            //    the SHA-256 compression function
            for($j = 0; $j < 64; $j++)
            {
                if($j < 16)
                {
                    $T1  = ord($chunk{$j*4  }) & 0xFF; $T1 <<= 8;
                    $T1 |= ord($chunk{$j*4+1}) & 0xFF; $T1 <<= 8;
                    $T1 |= ord($chunk{$j*4+2}) & 0xFF; $T1 <<= 8;
                    $T1 |= ord($chunk{$j*4+3}) & 0xFF;
                    $W[$j] = $T1;
                }
                else
                {
                    $W[$j] = SHA256::sum(((($W[$j-2] >> 17) & 0x00007FFF) | ($W[$j-2] << 15)) ^ ((($W[$j-2] >> 19) & 0x00001FFF) | ($W[$j-2] << 13)) ^ (($W[$j-2] >> 10) & 0x003FFFFF), $W[$j-7], ((($W[$j-15] >> 7) & 0x01FFFFFF) | ($W[$j-15] << 25)) ^ ((($W[$j-15] >> 18) & 0x00003FFF) | ($W[$j-15] << 14)) ^ (($W[$j-15] >> 3) & 0x1FFFFFFF), $W[$j-16]);
                }

                $T1 = SHA256::sum($h, ((($e >> 6) & 0x03FFFFFF) | ($e << 26)) ^ ((($e >> 11) & 0x001FFFFF) | ($e << 21)) ^ ((($e >> 25) & 0x0000007F) | ($e << 7)), ($e & $f) ^ (~$e & $g), $K[$j], $W[$j]);
                $T2 = SHA256::sum(((($a >> 2) & 0x3FFFFFFF) | ($a << 30)) ^ ((($a >> 13) & 0x0007FFFF) | ($a << 19)) ^ ((($a >> 22) & 0x000003FF) | ($a << 10)), ($a & $b) ^ ($a & $c) ^ ($b & $c));
                $h = $g;
                $g = $f;
                $f = $e;
                $e = SHA256::sum($d, $T1);
                $d = $c;
                $c = $b;
                $b = $a;
                $a = SHA256::sum($T1, $T2);
            }

            //    compute the next hash set
            for($j = 0; $j < 8; $j++)
                $hashData->hash[$j] = SHA256::sum(${$vars{$j}}, $hashData->hash[$j]);
        }
    }


    //    set up the display of the hash in hex.
    public function hashHex(&$hashData)
    {
        $str = '';

        reset($hashData->hash);
        do
        {
            $str .= sprintf('%08x', current($hashData->hash));
        }
        while(next($hashData->hash));

        return $str;
    }


    //    set up the output of the hash in binary
    public function hashBin(&$hashData)
    {
        $str = '';

        reset($hashData->hash);
        do
        {
            $str .= pack('N', current($hashData->hash));
        }
        while(next($hashData->hash));

        return $str;
    }


    //    set up the output of the hash in bits
    public function hashBit(&$hashData)
    {
        $str = '';

        reset($hashData->hash);
        do
        {
            $t = current($hashData->hash);
            for($i = 31; $i >= 0; $i--)
            {
                $str .= ($t & (1 << $i) ? '1' : '0');
            }
        }
        while(next($hashData->hash));

        return $str;
    }
}

/*==============================================================================
 *    SHA256 Data Class
 *============================================================================*/

class SHA256Data
{
    public function SHA256Data( $type, $str )
    {
        $type = 'SHA256Message' . $type;
        $this->message =& new $type( $str );

        //    H(0)
        $this->hash = array
        (
             1779033703,    -1150833019,
             1013904242,    -1521486534,
             1359893119,    -1694144372,
              528734635,     1541459225,
        );
    }
}

/*==============================================================================
 *    SHA256 Message Classes
 *============================================================================*/

class SHA256Message
{
    public function SHA256Message( $str )
    {
        $str .= $this->calculateFooter( strlen( $str ) );

        //    break the binary string into 512-bit blocks
        preg_match_all( '#.{64}#', $str, $this->chunk );
        $this->chunk = $this->chunk[0];

        $this->curChunk = -1;
    }

    //    retrieve the next chunk of the message
    public function nextChunk()
    {
        if( is_array($this->chunk) && ($this->curChunk >= -1) && isset($this->chunk[$this->curChunk + 1]) )
        {
            $this->curChunk++;
            $ret =& $this->chunk[$this->curChunk];
        }
        else
        {
            $this->chunk = null;
            $this->curChunk = -1;
            $ret = false;
        }

        return $ret;
    }

    //    retrieve the current chunk of the message
    public function currentChunk()
    {
        if( is_array($this->chunk) )
        {
            if( $this->curChunk == -1 )
            {
                $this->curChunk = 0;
            }
            if( ($this->curChunk >= 0) && isset($this->chunk[$this->curChunk]) )
            {
                $ret =& $this->chunk[$this->curChunk];
            }
        }
        else
        {
            $ret = false;
        }

        return $ret;
    }


    //    internal static function calculateFooter() which, calculates the footer appended to all messages
    public function calculateFooter( $numbytes )
    {
        $M =& $numbytes;
        $L1 = ($M >> 28) & 0x0000000F;    //    top order bits
        $L2 = $M << 3;    //    number of bits
        $l = pack('N*', $L1, $L2);

        //    64 = 64 bits needed for the size mark. 1 = the 1 bit added to the
        //    end. 511 = 511 bits to get the number to be at least large enough
        //    to require one block. 512 is the block size.
        $k = $L2 + 64 + 1 + 511;
        $k -= $k % 512 + $L2 + 64 + 1;
        $k >>= 3;    //    convert to byte count

        $footer = chr(128) . str_repeat(chr(0), $k) . $l;

        assert('($M + strlen($footer)) % 64 == 0');

        return $footer;
    }
}
?>
