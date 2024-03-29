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
// File: classes/Ibank.php
//
// TODO: These are horribly bad. They should be broken out of classes, and turned mostly into template
// behaviors. But in the interest of saying goodbye to the includes directory, and raw functions, this
// will at least allow us to auto-load and use classes instead. Plenty to do in the future, though!

namespace Bad;

class Ibank
{
    public static function ibankBorrow($db, $langvars, $playerinfo, $active_template)
    {
        global $account, $amount, $ibank_loanlimit, $ibank_loanfactor, $ibank_lrate;

        $amount = preg_replace("/[^0-9]/", "", $amount);
        if (($amount * 1) != $amount)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($amount <= 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($account['loan'] != 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notwoloans'], "igb.php?command=loans");
        }

        $score = \Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
        $maxtrans = $score * $score * $ibank_loanlimit;

        if ($amount > $maxtrans)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_loantoobig'], "igb.php?command=loans");
        }

        $amount2 = $amount * $ibank_loanfactor;
        $amount3 = $amount + $amount2;

        $hours = $ibank_lrate / 60;
        $mins = $ibank_lrate % 60;

        $langvars['l_ibank_loanreminder'] = str_replace("[hours]", $hours, $langvars['l_ibank_loanreminder']);
        $langvars['l_ibank_loanreminder'] = str_replace("[mins]", $mins, $langvars['l_ibank_loanreminder']);

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_takenaloan'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td colspan=2 align=center>" . $langvars['l_ibank_loancongrats'] . "<br><br></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_loantransferred'] . " :</td><td nowrap align=right>" . number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_loanfee'] . " :</td><td nowrap align=right>" . number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_amountowned'] . " :</td><td nowrap align=right>" . number_format($amount3, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>---------------------------------<br><br>" . $langvars['l_ibank_loanreminder'] . "<br><br>\"" . $langvars['l_ibank_loanreminder2'] ."\"</td>" .
             "<tr valign=top>" .
             "<td nowrap><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td nowrap align=right>&nbsp;<a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET loan = ?, loantime = NOW() WHERE ship_id = ?", array($amount3, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

        $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits = credits + ? WHERE ship_id = ?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    }

    public static function ibankLogin($langvars, $playerinfo, $account)
    {
        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_welcometoibank'] . "<br>---------------------------------</td></tr>" .
            "<tr valign=top>" .
             "<td width=150 align=right>" . $langvars['l_ibank_accountholder'] . " :<br><br>" . $langvars['l_ibank_shipaccount'] . " :<br>" . $langvars['l_ibank_ibankaccount'] . "&nbsp;&nbsp;:</td>" .
             "<td style='max-width:550px; padding-right:4px;' align=right>" . $playerinfo['character_name'] . "&nbsp;&nbsp;<br><br>" . number_format($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_ibank_credit_symbol'] . "<br>" . number_format($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " " . $langvars['l_ibank_credit_symbol'] . "<br></td>" .
             "</tr>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_operations'] . "<br>---------------------------------<br><br><a href=\"igb.php?command=withdraw\">" . $langvars['l_ibank_withdraw'] . "</a><br><a href=\"igb.php?command=deposit\">" . $langvars['l_ibank_deposit'] . "</a><br><a href=\"igb.php?command=transfer\">" . $langvars['l_ibank_transfer'] . "</a><br><a href=\"igb.php?command=loans\">" . $langvars['l_ibank_loans'] . "</a><br>&nbsp;</td></tr>" .
             "<tr valign=bottom>" .
             "<td align='left'><a href='igb.php'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankWithdraw($langvars, $playerinfo, $account)
    {
        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_withdrawfunds'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_fundsavailable'] . ":</td>" .
             "<td align=right>" . number_format($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) ." C<br></td>" .
             "</tr><tr valign=top>" .
             "<td>" . $langvars['l_ibank_selwithdrawamount'] . ":</td><td align=right>" .
             "<form accept-charset='utf-8' action='igb.php?command=withdraw2' method=post>" .
             "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
             "<br><br><input class=term type=submit value='" . $langvars['l_ibank_withdraw'] . "'>" .
             "</form></td></tr>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankWithdraw2($db, $langvars, $playerinfo, $amount, $account)
    {
        $amount = preg_replace("/[^0-9]/", "", $amount);
        if (($amount * 1) != $amount)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidwithdrawinput'], "igb.php?command=withdraw");
        }

        if ($amount == 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_nozeroamount3'], "igb.php?command=withdraw");
        }

        if ($amount > $account['balance'])
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notenoughcredits'], "igb.php?command=withdraw");
        }

        $account['balance'] -= $amount;
        $playerinfo['credits'] += $amount;

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_operationsuccessful'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>" . number_format($amount, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) ." " . $langvars['l_ibank_creditstoyourship'] . "</td>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_accounts'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>Ship Account :<br>" . $langvars['l_ibank_ibankaccount'] . " :</td>" .
             "<td align=right>" . number_format($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C<br>" . number_format($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</tr>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance - ? WHERE ship_id = ?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits=credits + ? WHERE ship_id = ?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    }

    public static function ibankTransfer($db, $langvars, $playerinfo, $ibank_min_turns)
    {
        $res = $db->Execute("SELECT character_name, ship_id FROM {$db->prefix}ships WHERE email not like '%@xenobe' AND ship_destroyed ='N' AND turns_used > ? ORDER BY character_name ASC", array($ibank_min_turns));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

        $ships = array();
        while (!$res->EOF)
        {
            $ships[] = $res->fields;
            $res->MoveNext();
        }

        $res = $db->Execute("SELECT name, planet_id, sector_id FROM {$db->prefix}planets WHERE owner=? ORDER BY sector_id ASC", array($playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        while (!$res->EOF)
        {
            $planets[] = $res->fields;
            $res->MoveNext();
        }

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfertype'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<form accept-charset='utf-8' action='igb.php?command=transfer2' method=post>" .
             "<td>" . $langvars['l_ibank_toanothership'] . " :<br><br>" .
             "<select class=term name=ship_id style='width:200px;'>";

        foreach($ships as $ship)
        {
            echo "<option value='" . $ship['ship_id'] . "'>" . $ship['character_name'] . "</option>";
        }

        echo "</select></td><td valign=center align=right>" .
             "<input class=term type=submit name=shipt value='" . $langvars['l_ibank_shiptransfer'] . "'>" .
             "</form>" .
             "</td></tr>" .
             "<tr valign=top>" .
             "<td><br>" . $langvars['l_ibank_fromplanet'] . " :<br><br>" .
             "<form accept-charset='utf-8' action='igb.php?command=transfer2' method=post>" .
             $langvars['l_ibank_source'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select class=term name=splanet_id>";

        if (isset($planets))
        {
            foreach($planets as $planet)
            {
                if (empty($planet['name']))
                {
                    $planet['name'] = $langvars['l_ibank_unnamed'];
                }
                echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
            }
        }
        else
        {
            echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
        }

        echo "</select><br>" . $langvars['l_ibank_destination'] . "<select class=term name=dplanet_id>";

        if (isset($planets))
        {
            foreach ($planets as $planet)
            {
                if (empty($planet['name']))
                {
                    $planet['name'] = $langvars['l_ibank_unnamed'];
                }
                echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
            }
        }
        else
        {
            echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
        }

        echo "</select></td><td valign=center align=right>" .
             "<br><input class=term type=submit name=planett value='" . $langvars['l_ibank_planettransfer'] . "'>" .
             "</td></tr>" .
             "</form>";

        // ---- begin Consol Credits form    // ---- added by Torr
        echo "<tr valign=top>" .
             "<td><br>" . $langvars['l_ibank_conspl'] . " :<br><br>" .
             "<form accept-charset='utf-8' action='igb.php?command=consolidate' method=post>" .
             $langvars['l_ibank_destination'] . " <select class=term name=dplanet_id>";

        if (isset($planets))
        {
            foreach ($planets as $planet)
            {
                if (empty($planet['name']))
                {
                    $planet['name'] = $langvars['l_ibank_unnamed'];
                }
                echo "<option value=" . $planet['planet_id'] . ">" . $planet['name'] . " " . $langvars['l_ibank_in'] . " " . $planet['sector_id'] . "</option>";
            }
        }
        else
        {
            echo "<option value=none>" . $langvars['l_ibank_none'] . "</option>";
        }

        echo "</select></td><td valign=top align=right>" .
             "<br><input class=term type=submit name=planetc value='" . $langvars['l_ibank_consolidate'] . "'>" .
             "</td></tr>" .
             "</form>";
        // ---- End Consol Credits form ---

        echo "</form><tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankLoans($db, $langvars, $bntreg, $playerinfo)
    {
        global $ibank_loanlimit, $ibank_loanfactor, $ibank_loaninterest, $account;

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_loanstatus'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top><td>" . $langvars['l_ibank_shipaccount'] . " :</td><td align=right>" . number_format($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td></tr>" .
             "<tr valign=top><td>" . $langvars['l_ibank_currentloan'] . " :</td><td align=right>" . number_format($account['loan'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td></tr>";

        if ($account['loan'] != 0)
        {
            $curtime = time();
            $res = $db->Execute("SELECT UNIX_TIMESTAMP(loantime) as time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array($playerinfo['ship_id']));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if (!$res->EOF)
            {
                $time = $res->fields;
            }

            $difftime = ($curtime - $time['time']) / 60;

            echo "<tr valign=top><td nowrap>" . $langvars['l_ibank_loantimeleft'] . " :</td>";

            if ($difftime > $ibank_lrate)
            {
                echo "<td align=right>" . $langvars['l_ibank_loanlate'] . "</td></tr>";
            }
            else
            {
                $difftime = $ibank_lrate - $difftime;
                $hours = $difftime / 60;
                $hours = (int) $hours;
                $mins = $difftime % 60;
                echo "<td align=right>{$hours}h {$mins}m</td></tr>";
            }

            $factor = $ibank_loanfactor *= 100;
            $interest = $ibank_loaninterest *= 100;

            $langvars['l_ibank_loanrates'] = str_replace("[factor]", $factor, $langvars['l_ibank_loanrates']);
            $langvars['l_ibank_loanrates'] = str_replace("[interest]", $interest, $langvars['l_ibank_loanrates']);

            echo "<form accept-charset='utf-8' action='igb.php?command=repay' method=post>" .
                 "<tr valign=top>" .
                 "<td><br>" . $langvars['l_ibank_repayamount'] . " :</td>" .
                 "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
                 "<br><input class=term type=submit value='" . $langvars['l_ibank_repay'] . "'></td>" .
                 "</form>" .
                 "<tr><td colspan=2 align=center>" . $langvars['l_ibank_loanrates'];
        }
        else
        {
            $percent = $ibank_loanlimit * 100;
            $score = \Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
            $maxloan = $score * $score * $ibank_loanlimit;

            $langvars['l_ibank_maxloanpercent'] = str_replace("[ibank_percent]", $percent, $langvars['l_ibank_maxloanpercent']);
            echo "<tr valign=top><td nowrap>" . $langvars['l_ibank_maxloanpercent'] . " :</td><td align=right>" . number_format($maxloan, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td></tr>";

            $factor = $ibank_loanfactor *= 100;
            $interest = $ibank_loaninterest *= 100;

            $langvars['l_ibank_loanrates'] = str_replace("[factor]", $factor, $langvars['l_ibank_loanrates']);
            $langvars['l_ibank_loanrates'] = str_replace("[interest]", $interest, $langvars['l_ibank_loanrates']);

            echo "<form accept-charset='utf-8' action='igb.php?command=borrow' method=post>" .
                 "<tr valign=top>" .
                 "<td><br>" . $langvars['l_ibank_loanamount'] . " :</td>" .
                 "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
                 "<br><input class=term type=submit value='" . $langvars['l_ibank_borrow'] . "'></td>" .
                 "</form>" .
                 "<tr><td colspan=2 align=center>" . $langvars['l_ibank_loanrates'];
        }

        echo "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankRepay($db, $langvars, $playerinfo, $account, $amount)
    {
        $amount = preg_replace("/[^0-9]/", "", $amount);
        if (($amount * 1) != $amount)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($amount <= 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidamount'], "igb.php?command=loans");
        }

        if ($account['loan'] == 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notrepay'], "igb.php?command=loans");
        }

        if ($amount > $account['loan'])
        {
            $amount = $account['loan'];
        }

        if ($amount > $playerinfo['credits'])
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notenoughrepay'], "igb.php?command=loans");
        }

        $playerinfo['credits'] -= $amount;
        $account['loan'] -= $amount;

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_payloan'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>" . $langvars['l_ibank_loanthanks'] . "</td>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>---------------------------------</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_shipaccount'] . " :</td><td nowrap align=right>" . number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_payloan'] . " :</td><td nowrap align=right>" . number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_currentloan'] . " :</td><td nowrap align=right>" . number_format($account['loan'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>---------------------------------</td>" .
             "<tr valign=top>" .
             "<td nowrap><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td nowrap align=right>&nbsp;<a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET loan=loan - ?, loantime=? WHERE ship_id = ?", array($amount, $account['loantime'], $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits=credits - ? WHERE ship_id = ?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    }

    public static function ibankConsolidate($langvars)
    {
        global $dplanet_id, $ibank_tconsolidate, $ibank_paymentfee;

        $percent = $ibank_paymentfee * 100;

        $langvars['l_ibank_transferrate3'] = str_replace("[ibank_num_percent]", number_format($percent, 1, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_ibank_transferrate3']);
        $langvars['l_ibank_transferrate3'] = str_replace("[nbplanets]", $ibank_tconsolidate, $langvars['l_ibank_transferrate3']);

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_planetconsolidate'] . "<br>---------------------------------</td></tr>" .
             "<form accept-charset='utf-8' action='igb.php?command=consolidate2' method=post>" .
             "<tr valign=top>" .
             "<td colspan=2>" . $langvars['l_ibank_consolrates'] . " :</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_minimum'] . " :<br>" .
             "<br>" . $langvars['l_ibank_maximum'] . " :</td>" .
             "<td align=right>" .
             "<input class=term type=text size=15 maxlength=20 name=minimum value=0><br><br>" .
             "<input class=term type=text size=15 maxlength=20 name=maximum value=0><br><br>" .
             "<input class=term type=submit value=\"" . $langvars['l_ibank_compute'] . "\"></td>" .
             "<input type=hidden name=dplanet_id value=" . $dplanet_id . ">" .
             "</form>" .
             "<tr><td colspan=2 align=center>" .
             $langvars['l_ibank_transferrate3'] .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankTransfer2($db, $langvars)
    {
        global $playerinfo, $account, $ship_id, $splanet_id, $dplanet_id, $ibank_min_turns, $ibank_svalue;
        global $ibank_paymentfee, $ibank_trate;

        if (isset($ship_id)) // Ship transfer
        {
            $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=? AND ship_destroyed ='N' AND turns_used > ?;", array($ship_id, $ibank_min_turns));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

            if ($playerinfo['ship_id'] == $ship_id)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_sendyourself'], "igb.php?command=transfer");
            }

            if (!$res instanceof ADORecordSet || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_unknowntargetship'], "igb.php?command=transfer");
            }

            $target = $res->fields;

            if ($target['turns_used'] < $ibank_min_turns)
            {
                $langvars['l_ibank_min_turns'] = str_replace("[ibank_min_turns]", $ibank_min_turns, $langvars['l_ibank_min_turns']);
                $langvars['l_ibank_min_turns'] = str_replace("[ibank_target_char_name]", $target['character_name'], $langvars['l_ibank_min_turns']);
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_min_turns'], "igb.php?command=transfer");
            }

            if ($playerinfo['turns_used'] < $ibank_min_turns)
            {
                $langvars['l_ibank_min_turns2'] = str_replace("[ibank_min_turns]", $ibank_min_turns, $langvars['l_ibank_min_turns2']);
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_min_turns2'], "igb.php?command=transfer");
            }

            if ($ibank_trate > 0)
            {
                $curtime = time();
                $curtime -= $ibank_trate * 60;
                $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}ibank_transfers WHERE UNIX_TIMESTAMP(time) > ? AND source_id = ? AND dest_id = ?", array($curtime, $playerinfo['ship_id'], $target['ship_id']));
                \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
                if (!$res->EOF)
                {
                    $time = $res->fields;
                    $difftime = ($time['time'] - $curtime) / 60;
                    $langvars['l_ibank_mustwait'] = str_replace("[ibank_target_char_name]", $target['character_name'], $langvars['l_ibank_mustwait']);
                    $langvars['l_ibank_mustwait'] = str_replace("[ibank_trate]", number_format($ibank_trate, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']), $langvars['l_ibank_mustwait']);
                    $langvars['l_ibank_mustwait'] = str_replace("[ibank_difftime]", number_format($difftime, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']), $langvars['l_ibank_mustwait']);
                    Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_mustwait'], "igb.php?command=transfer");
                }
            }

            echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_shiptransfer'] . "<br>---------------------------------</td></tr>" .
                 "<tr valign=top><td>" . $langvars['l_ibank_ibankaccount'] . " :</td><td align=right>" . number_format($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td></tr>";

            if ($ibank_svalue == 0)
            {
                echo "<tr valign=top><td>" . $langvars['l_ibank_maxtransfer'] . " :</td><td align=right>" . $langvars['l_ibank_unlimited'] . "</td></tr>";
            }
            else
            {
                $percent = $ibank_svalue * 100;
                $score = \Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
                $maxtrans = $score * $score * $ibank_svalue;

                $langvars['l_ibank_maxtransferpercent'] = str_replace("[ibank_percent]", $percent, $langvars['l_ibank_maxtransferpercent']);
                echo "<tr valign=top><td nowrap>" . $langvars['l_ibank_maxtransferpercent'] . " :</td><td align=right>" . number_format($maxtrans, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td></tr>";
            }

            $percent = $ibank_paymentfee * 100;

            $langvars['l_ibank_transferrate'] = str_replace("[ibank_num_percent]", number_format($percent, 1, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']), $langvars['l_ibank_transferrate']);
            echo "<tr valign=top><td>" . $langvars['l_ibank_recipient'] . " :</td><td align=right>" . $target['character_name'] . "&nbsp;&nbsp;</td></tr>" .
                 "<form accept-charset='utf-8' action='igb.php?command=transfer3' method=post>" .
                 "<tr valign=top>" .
                 "<td><br>" . $langvars['l_ibank_seltransferamount'] . " :</td>" .
                 "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
                 "<br><input class=term type=submit value='" . $langvars['l_ibank_transfer'] . "'></td>" .
                 "<input type=hidden name=ship_id value='" . $ship_id . "'>" .
                 "</form>" .
                 "<tr><td colspan=2 align=center>" . $langvars['l_ibank_transferrate'] .
                 "<tr valign=bottom>" .
                 "<td><a href='igb.php?command=transfer'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
                 "</tr>";
        }
        else
        {
            if ($splanet_id == $dplanet_id)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errplanetsrcanddest'], "igb.php?command=transfer");
            }

            $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array($splanet_id));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if (!$res || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
            }

            $source = $res->fields;

            if (empty($source['name']))
            {
                $source['name'] = $langvars['l_ibank_unnamed'];
            }

            $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM {$db->prefix}planets WHERE planet_id = ?", array($dplanet_id));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if (!$res || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
            }

            $dest = $res->fields;

            if (empty($dest['name']))
            {
                $dest['name'] = $langvars['l_ibank_unnamed'];
            }

            if ($dest['base'] == 'N')
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errnobase'], "igb.php?command=transfer");
            }

            if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errnotyourplanet'], "igb.php?command=transfer");
            }

            $percent = $ibank_paymentfee * 100;

            $langvars['l_ibank_transferrate2'] = str_replace("[ibank_num_percent]", number_format($percent, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']), $langvars['l_ibank_transferrate2']);
            echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_planettransfer'] . "<br>---------------------------------</td></tr>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_srcplanet'] . " " . $source['name'] . " " . $langvars['l_ibank_in'] . " " . $source['sector_id'] . " :" .
                 "<td align=right>" . number_format($source['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_destplanet'] . " " . $dest['name'] . " " . $langvars['l_ibank_in'] . " " . $dest['sector_id'] . " :" .
                 "<td align=right>" . number_format($dest['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C" .
                 "<form accept-charset='utf-8' action='igb.php?command=transfer3' method=post>" .
                 "<tr valign=top>" .
                 "<td><br>" . $langvars['l_ibank_seltransferamount'] . " :</td>" .
                 "<td align=right><br><input class=term type=text size=15 maxlength=20 name=amount value=0><br>" .
                 "<br><input class=term type=submit value='" . $langvars['l_ibank_transfer'] . "'></td>" .
                 "<input type=hidden name=splanet_id value='" . $splanet_id . "'>" .
                 "<input type=hidden name=dplanet_id value='" . $dplanet_id . "'>" .
                 "</form>" .
                 "<tr><td colspan=2 align=center>" . $langvars['l_ibank_transferrate2'] .
                 "<tr valign=bottom>" .
                 "<td><a href='igb.php?command=transfer'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
                 "</tr>";
        }
    }

    public static function ibankTransfer3($db, $langvars)
    {
        global $playerinfo, $account, $ship_id, $splanet_id, $dplanet_id, $ibank_min_turns, $ibank_svalue;
        global $ibank_paymentfee, $amount, $ibank_trate;

        $amount = preg_replace("/[^0-9]/", "", $amount);

        if ($amount < 0)
        {
            $amount = 0;
        }

        if (isset($ship_id)) //ship transfer
        {
            // Need to check again to prevent cheating by manual posts

            $res = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id = ? AND ship_destroyed ='N' AND turns_used > ?", array($ship_id, $ibank_min_turns));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

            if ($playerinfo['ship_id'] == $ship_id)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errsendyourself'], "igb.php?command=transfer");
            }

            if (!$res || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_unknowntargetship'], "igb.php?command=transfer");
            }

            $target = $res->fields;

            if ($target['turns_used'] < $ibank_min_turns)
            {
                $langvars['l_ibank_min_turns3'] = str_replace("[ibank_min_turns]", $ibank_min_turns, $langvars['l_ibank_min_turns3']);
                $langvars['l_ibank_min_turns3'] = str_replace("[ibank_target_char_name]", $target['character_name'], $langvars['l_ibank_min_turns3']);
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_min_turns3'], "igb.php?command=transfer");
            }

            if ($playerinfo['turns_used'] < $ibank_min_turns)
            {
                $langvars['l_ibank_min_turns4'] = str_replace("[ibank_min_turns]", $ibank_min_turns, $langvars['l_ibank_min_turns4']);
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_min_turns4'], "igb.php?command=transfer");
            }

            if ($ibank_trate > 0)
            {
                $curtime = time();
                $curtime -= $ibank_trate * 60;
                $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM {$db->prefix}ibank_transfers WHERE UNIX_TIMESTAMP(time) > ? AND source_id = ? AND dest_id = ?", array($curtime, $playerinfo['ship_id'], $target['ship_id']));
                \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
                if (!$res->EOF)
                {
                    $time = $res->fields;
                    $difftime = ($time['time'] - $curtime) / 60;
                    $langvars['l_ibank_mustwait2'] = str_replace("[ibank_target_char_name]", $target['character_name'], $langvars['l_ibank_mustwait2']);
                    $langvars['l_ibank_mustwait2'] = str_replace("[ibank_trate]", number_format($ibank_trate, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_ibank_mustwait2']);
                    $langvars['l_ibank_mustwait2'] = str_replace("[ibank_difftime]", number_format($difftime, 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_ibank_mustwait2']);
                    Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_mustwait2'], "igb.php?command=transfer");
                }
            }

            if (($amount * 1) != $amount)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invalidtransferinput'], "igb.php?command=transfer");
            }

            if ($amount == 0)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_nozeroamount'], "igb.php?command=transfer");
            }

            if ($amount > $account['balance'])
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notenoughcredits'], "igb.php?command=transfer");
            }

            if ($ibank_svalue != 0)
            {
                $percent = $ibank_svalue * 100;
                $score = \Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
                $maxtrans = $score * $score * $ibank_svalue;

                if ($amount > $maxtrans)
                {
                    Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_amounttoogreat'], "igb.php?command=transfer");
                }
            }

            $account['balance'] -= $amount;
            $amount2 = $amount * $ibank_paymentfee;
            $transfer = $amount - $amount2;

            echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfersuccessful'] . "<br>---------------------------------</td></tr>" .
                 "<tr valign=top><td colspan=2 align=center>" . number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $langvars['l_ibank_creditsto'] . " " . $target['character_name'] . " .</tr>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_transferamount'] . " :</td><td align=right>" . number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_transferfee'] . " :</td><td align=right>" . number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_amounttransferred'] . " :</td><td align=right>" . number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_ibankaccount'] . " :</td><td align=right>" . number_format($account['balance'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=bottom>" .
                 "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
                 "</tr>";

            $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance - ? WHERE ship_id = ?", array($amount, $playerinfo['ship_id']));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
            $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance + ? WHERE ship_id = ?", array($transfer, $target['ship_id']));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

            $resx = $db->Execute("INSERT INTO {$db->prefix}ibank_transfers VALUES (NULL, ?, ?, NOW(), ?)", array($playerinfo['ship_id'], $target['ship_id'], $transfer));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        }
        else
        {
            if ($splanet_id == $dplanet_id)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errplanetsrcanddest'], "igb.php?command=transfer");
            }

            $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array($splanet_id));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if (!$res || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
            }

            $source = $res->fields;

            if (empty($source['name']))
            {
                $source['name'] = $langvars['l_ibank_unnamed'];
            }

            $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array($dplanet_id));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if (!$res || $res->EOF)
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
            }

            $dest = $res->fields;

            if (empty($dest['name']))
            {
                $dest['name'] = $langvars['l_ibank_unnamed'];
            }

            if ($source['owner'] != $playerinfo['ship_id'] || $dest['owner'] != $playerinfo['ship_id'])
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errnotyourplanet'], "igb.php?command=transfer");
            }

            if ($amount > $source['credits'])
            {
                Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notenoughcredits2'], "igb.php?command=transfer");
            }

            $percent = $ibank_paymentfee * 100;

            $source['credits'] -= $amount;
            $amount2 = $amount * $ibank_paymentfee;
            $transfer = $amount - $amount2;
            $dest['credits'] += $transfer;

            echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfersuccessful'] . "<br>---------------------------------</td></tr>" .
                 "<tr valign=top><td colspan=2 align=center>" . number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep) . " " . $langvars['l_ibank_ctransferredfrom'] . " " . $source['name'] . " " . $langvars['l_ibank_to'] . " " . $dest['name'] . ".</tr>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_transferamount'] . " :</td><td align=right>" . number_format($amount, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_transferfee'] . " :</td><td align=right>" . number_format($amount2, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_amounttransferred'] . " :</td><td align=right>" . number_format($transfer, 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_srcplanet'] . " " . $source['name'] . " " . $langvars['l_ibank_in'] . " " . $source['sector_id'] . " :</td><td align=right>" . number_format($source['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=top>" .
                 "<td>" . $langvars['l_ibank_destplanet'] . " " . $dest['name'] . " " . $langvars['l_ibank_in'] . " " . $dest['sector_id'] . " :</td><td align=right>" . number_format($dest['credits'], 0, $local_number_dec_point, $local_number_thousands_sep) . " C<br>" .
                 "<tr valign=bottom>" .
                 "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
                 "</tr>";

            $resx = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits - ? WHERE planet_id = ?", array($amount, $splanet_id));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
            $resx = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits + ? WHERE planet_id = ?", array($transfer, $dplanet_id));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        }
    }

    public static function ibankDeposit2($db, $langvars, $playerinfo, $amount, $account)
    {

        $max_credits_allowed = 18446744073709000000;

        $amount = preg_replace("/[^0-9]/", "", $amount);

        if (($amount * 1) != $amount)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_invaliddepositinput'], "igb.php?command=deposit");
        }

        if ($amount == 0)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_nozeroamount2'], "igb.php?command=deposit");
        }

        if ($amount > $playerinfo['credits'])
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_notenoughcredits'], "igb.php?command=deposit");
        }

        $tmpcredits = $max_credits_allowed - $account['balance'];
        if ($tmpcredits < 0)
        {
            $tmpcredits = 0;
        }

        if ($amount > $tmpcredits)
        {
            Ibank::ibankError($active_template, $langvars, "<center>Error You cannot deposit that much into your bank,<br> (Max Credits Reached)</center>", "igb.php?command=deposit");
        }

        $account['balance'] += $amount;
        $playerinfo['credits'] -= $amount;

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_operationsuccessful'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>" . number_format($amount, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) ." " . $langvars['l_ibank_creditstoyou'] . "</td>" .
             "<tr><td colspan=2 align=center>" . $langvars['l_ibank_accounts'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_shipaccount'] . " :<br>" . $langvars['l_ibank_ibankaccount'] . " :</td>" .
             "<td align=right>" . number_format($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C<br>" . number_format($account['balance'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</tr>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";

        $resx = $db->Execute("UPDATE {$db->prefix}ibank_accounts SET balance = balance + ? WHERE ship_id=?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        $resx = $db->Execute("UPDATE {$db->prefix}ships SET credits = credits - ? WHERE ship_id=?", array($amount, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    }

    public static function ibankConsolidate2($db, $langvars, $playerinfo)
    {
        global $account;
        global $dplanet_id, $minimum, $maximum, $ibank_tconsolidate, $ibank_paymentfee;

        $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array($dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

        if (!$res || $res->EOF)
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
        }
        $dest = $res->fields;

        if (empty($dest['name']))
        {
            $dest['name'] = $langvars['l_ibank_unnamed'];
        }

        if ($dest['owner'] != $playerinfo['ship_id'])
        {
            Ibank::ibankError($active_template, $langvars, $langvars['l_ibank_errnotyourplanet'], "igb.php?command=transfer");
        }

        $minimum = preg_replace("/[^0-9]/", "", $minimum);
        $maximum = preg_replace("/[^0-9]/", "", $maximum);

        $query = "SELECT SUM(credits) AS total, COUNT(*) AS count FROM {$db->prefix}planets WHERE owner=? AND credits != 0 AND planet_id != ?";

        if ($minimum != 0)
        {
            $query .= " AND credits >= $minimum";
        }

        if ($maximum != 0)
        {
            $query .= " AND credits <= $maximum";
        }

        $res = $db->Execute($query, array($playerinfo['ship_id'], $dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $amount = $res->fields;

        $fee = $ibank_paymentfee * $amount['total'];

        $tcost = ceil($amount['count'] / $ibank_tconsolidate);
        $transfer = $amount['total'] - $fee;

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_planetconsolidate'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_currentpl'] . " " . $dest['name'] . " " . $langvars['l_ibank_in'] . " " . $dest['sector_id'] . " :</td>" .
             "<td align=right>" . number_format($dest['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_transferamount'] . " :</td>" .
             "<td align=right>" . number_format($amount['total'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_transferfee'] . " :</td>" .
             "<td align=right>" . number_format($fee, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C </td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_plaffected'] . " :</td>" .
             "<td align=right>" . number_format($amount['count'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . "</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_turncost'] . " :</td>" .
             "<td align=right>" . number_format($tcost, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . "</td>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_amounttransferred'] . ":</td>" .
             "<td align=right>" . number_format($transfer, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C</td>" .
             "<tr valign=top><td colspan=2 align=right>" .
             "<form accept-charset='utf-8' action='igb.php?command=consolidate3' method=post>" .
             "<input type=hidden name=minimum value=" . $minimum . "><br>" .
             "<input type=hidden name=maximum value=" . $maximum . "><br>" .
             "<input type=hidden name=dplanet_id value=" . $dplanet_id . ">" .
             "<input class=term type=submit value=\"" . $langvars['l_ibank_consolidate'] . "\"></td>" .
             "</form>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=transfer'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankError($active_template, $langvars, $errmsg, $backlink, $title = "Error!")
    {
        $title = $langvars['l_ibank_ibankerrreport'];
        echo "<tr><td colspan=2 align=center valign=top>" . $title . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td colspan=2 align=center>" . $errmsg . "</td>" .
             "</tr>" .
             "<tr valign=bottom>" .
             "<td><a href=" . $backlink . ">" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>" .
             "</table>" .
             "</td></tr>" .
             "</table>" .
             "<img width=600 height=21 src=" . $active_template . "/images/div2.png>" .
             "</center>";

        \Bnt\Footer::display($pdo_db, $lang, $bntreg, $template);
        die();
    }

    public static function isLoanPending($db, $ship_id, $ibank_lrate)
    {
        $res = $db->Execute("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM {$db->prefix}ibank_accounts WHERE ship_id = ?", array($ship_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        if ($res)
        {
            $account = $res->fields;

            if ($account['loan'] == 0)
            {
                return false;
            }

            $curtime = time();
            $difftime = ($curtime - $account['time']) / 60;
            if ($difftime > $ibank_lrate)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public static function deposit($db, $pdo_db, $lang, $account, $playerinfo, $langvars)
    {
        // Database driven language entries
        $langvars = \Bnt\Translate::load($pdo_db, $lang, array('igb'));

        $max_credits_allowed = 18446744073709000000;
        $credit_space = ($max_credits_allowed - $account['balance']);

        if ($credit_space > $playerinfo['credits'])
        {
            $credit_space = ($playerinfo['credits']);
        }

        if ($credit_space < 0)
        {
            $credit_space = 0;
        }

        echo "<tr><td height=53 colspan=2 align=center valign=top>" . $langvars['l_ibank_depositfunds'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td height=30>" . $langvars['l_ibank_fundsavailable'] . " :</td>" .
             "<td align=right>" . number_format($playerinfo['credits'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) ." C<br></td>" .
             "</tr><tr valign=top>" .
             "<td height=90>" . $langvars['l_ibank_seldepositamount'] . " :</td><td align=right>" .
             "<form accept-charset='utf-8' action='igb.php?command=deposit2' method=post>" .
             "<input class=term type=text size=15 maxlength=20 name=amount value=0>" .
             "<br><br><input class=term type=submit value=" . $langvars['l_ibank_deposit'] . ">" .
             "</form>" .
             "</td></tr>" .
             "<tr>" .
             "  <td height=30  colspan=2 align=left>" .
             "    <span style='color:\"#00ff00\";'>You can deposit only ". number_format($credit_space, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep'])." credits.</span><br>" .
             "  </td>" .
             "</tr>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout'] . "</a></td>" .
             "</tr>";
    }

    public static function ibankConsolidate3($db, $langvars, $playerinfo)
    {
        global $dplanet_id, $minimum, $maximum, $ibank_tconsolidate, $ibank_paymentfee;

        $res = $db->Execute("SELECT name, credits, owner, sector_id FROM {$db->prefix}planets WHERE planet_id = ?", array($dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        if (!$res || $res->EOF)
        {
            Ibank::ibankError($active_template, $active_template, $langvars, $langvars['l_ibank_errunknownplanet'], "igb.php?command=transfer");
        }

        $dest = $res->fields;

        if (empty($dest['name']))
        {
            $dest['name'] = $langvars['l_ibank_unnamed'];
        }

        if ($dest['owner'] != $playerinfo['ship_id'])
        {
            Ibank::ibankError($active_template, $active_template, $langvars, $langvars['l_ibank_errnotyourplanet'], "igb.php?command=transfer");
        }

        $minimum = preg_replace("/[^0-9]/", "", $minimum);
        $maximum = preg_replace("/[^0-9]/", "", $maximum);

        $query = "SELECT SUM(credits) as total, COUNT(*) AS count FROM {$db->prefix}planets WHERE owner=? AND credits != 0 AND planet_id != ?";

        if ($minimum != 0)
        {
            $query .= " AND credits >= $minimum";
        }

        if ($maximum != 0)
        {
            $query .= " AND credits <= $maximum";
        }

        $res = $db->Execute($query, array($playerinfo['ship_id'], $dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $amount = $res->fields;

        $fee = $ibank_paymentfee * $amount['total'];

        $tcost = ceil($amount['count'] / $ibank_tconsolidate);
        $transfer = $amount['total'] - $fee;

        $cplanet = $transfer + $dest['credits'];

        if ($tcost > $playerinfo['turns'])
        {
            Ibank::ibankError($active_template, $active_template, $langvars, $langvars['l_ibank_notenturns'], "igb.php?command=transfer");
        }

        echo "<tr><td colspan=2 align=center valign=top>" . $langvars['l_ibank_transfersuccessful'] . "<br>---------------------------------</td></tr>" .
             "<tr valign=top>" .
             "<td>" . $langvars['l_ibank_currentpl'] . " " . $dest['name'] . " " . $langvars['l_ibank_in'] . " " . $dest['sector_id'] . " :<br><br>" .
             $langvars['l_ibank_turncost'] . " :</td>" .
             "<td align=right>" . number_format($cplanet, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . " C<br><br>" .
             number_format($tcost, 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']) . "</td>" .
             "<tr valign=bottom>" .
             "<td><a href='igb.php?command=login'>" . $langvars['l_ibank_back'] . "</a></td><td align=right>&nbsp;<br><a href=\"main.php\">" . $langvars['l_ibank_logout ']. "</a></td>" .
             "</tr>";

        $query = "UPDATE {$db->prefix}planets SET credits=0 WHERE owner=? AND credits != 0 AND planet_id != ?";

        if ($minimum != 0)
        {
            $query .= " AND credits >= $minimum";
        }

        if ($maximum != 0)
        {
            $query .= " AND credits <= $maximum";
        }

        $res = $db->Execute($query, array($playerinfo['ship_id'], $dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $res = $db->Execute("UPDATE {$db->prefix}planets SET credits=credits + ? WHERE planet_id=?", array($transfer, $dplanet_id));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $res = $db->Execute("UPDATE {$db->prefix}ships SET turns=turns - ? WHERE ship_id=?", array($tcost, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    }
}
?>
