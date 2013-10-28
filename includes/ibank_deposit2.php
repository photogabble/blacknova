<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2012 Ron Harwood and the BNT development team
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
// File: includes/ibank_deposit2.php

if (strpos ($_SERVER['PHP_SELF'], 'ibank_deposit2.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

function ibank_deposit2 ($db, $langvars, $playerinfo)
{
    global $amount, $account;

    $max_credits_allowed = 18446744073709000000;

    $amount = preg_replace ("/[^0-9]/", "", $amount);

    if (($amount * 1) != $amount)
    {
        ibank_error ($langvars, $langvars['l_ibank_invaliddepositinput'], "igb.php?command=deposit");
    }

    if ($amount == 0)
    {
        ibank_error ($langvars, $langvars['l_ibank_nozeroamount2'], "igb.php?command=deposit");
    }

    if ($amount > $playerinfo['credits'])
    {
        ibank_error ($langvars, $langvars['l_ibank_notenoughcredits'], "igb.php?command=deposit");
    }

    $tmpcredits = $max_credits_allowed - $account['balance'];
    if ($tmpcredits < 0)
    {
        $tmpcredits = 0;
    }

    if ($amount > $tmpcredits)
    {
        ibank_error ($langvars, "<center>Error You cannot deposit that much into your bank,<br> (Max Credits Reached)</center>", "igb.php?command=deposit");
    }

    $account['balance'] += $amount;
    $playerinfo['credits'] -= $amount;

    echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_operationsuccessful'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td colspan=2 align=center>" . number_format ($amount, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) ." " . $langvars['l_ibank_creditstoyou'] . "</td>" .
         "<tr><td colspan=2 align=center>" . $langvars['l_ibank_accounts'] . "<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td>" . $langvars['l_ibank_shipaccount'] . " :<br>" . $langvars['l_ibank_ibankaccount'] . " :</td>" .
         "<td align=right>" . number_format ($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C<br>" . number_format ($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</tr>" .
         "<tr valign=bottom>" .
         "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
         "</tr>";

    $resx = $db->Execute ("UPDATE {$db->prefix}ibank_accounts SET balance=balance+? WHERE ship_id=?", array ($amount, $playerinfo['ship_id']));
    BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);
    $resx = $db->Execute ("UPDATE {$db->prefix}ships SET credits=credits-? WHERE ship_id=?", array ($amount, $playerinfo['ship_id']));
    BntDb::logDbErrors ($db, $resx, __LINE__, __FILE__);
}
?>
