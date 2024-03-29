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
// File: classes/Timer.php

namespace Bnt;

class Timer
{
    public $t_start = 0;
    public $t_stop = 0;
    public $t_elapsed = 0;

    public function start()
    {
        $this->t_start = microtime(true);
    }

    public function stop()
    {
        $this->t_stop  = microtime(true);
    }

    public function elapsed()
    {

        $this->t_elapsed = $this->t_stop - $this->t_start;

        return round($this->t_elapsed, 2); // Round it down to two significant digits
    }
}
?>
