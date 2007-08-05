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
// File: includes/phpmailer.php
//
// Include the phpmailer backend

include_once ("./backends/phpmailer/class.phpmailer.php");

class bnt_mailer extends phpmailer // This handles mail for the game.
{
    var $From;
    var $FromName;
    var $WordWrap;
    var $Host;
    var $Mailer;

    function bnt_mailer()
    {
        $this->WordWrap = 75;
        $this->CharSet = "UTF-8";
        $this->SetLanguage("en","backends/phpmailer/language/");
    }
}
?>
