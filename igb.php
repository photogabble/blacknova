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
// File: igb.php

include './global_includes.php';

// TODO: This should not be hard-coded, but for now, I need to be able to clear the errors
$active_template = 'classic';
BntLogin::checkLogin ($db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = BntTranslate::load ($db, $lang, array ('igb', 'common', 'global_includes', 'global_funcs', 'footer', 'news', 'regional'));

$title = $langvars['l_ibank_title'];
$body_class = 'igb';
include './header.php';

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result = $db->Execute ("SELECT * FROM {$db->prefix}ibank_accounts WHERE ship_id = ?;", array ($playerinfo['ship_id']));
BntDb::logDbErrors ($db, $result, __LINE__, __FILE__);
$account = $result->fields;

echo "<body class='" . $body_class . "'>";
echo "<center>";
echo '<img src="' . $template->GetVariables('template_dir') . '/images/div1.png" alt="" style="width: 600px; height:21px">';
echo '<div style="width:600px; max-width:600px;" class="igb">';
echo '<table style="width:600px; height:350px;" border="0px">';
echo '<tr><td style="background-image:URL(' . $template->GetVariables('template_dir') . '/images/igbscreen.png); background-repeat:no-repeat;" align="center">';
echo '<table style="width:550px; height:300px;" border="0px">';

if (!$bntreg->allow_ibank)
{
    BadIbank::ibankError ($template->GetVariables('template_dir'), $langvars, $langvars['l_ibank_malfunction'], "main.php");
}

if (!isset ($_REQUEST['command']))
{
    $_REQUEST['command'] = '';
    $command = '';
}
else
{
    $command = $_REQUEST['command'];
}

if ($command == 'login') //main menu
{
    BadIbank::ibankLogin ($langvars, $playerinfo, $account);
}
elseif ($command == 'withdraw') //withdraw menu
{
    BadIbank::ibankWithdraw ($langvars, $playerinfo, $account);
}
elseif ($command == 'withdraw2') //withdraw operation
{
    BadIbank::ibankWithdraw2 ($db, $langvars, $playerinfo);
}
elseif ($command == 'deposit') //deposit menu
{
    BadIbank::deposit ($db, $lang, $account, $playerinfo, $langvars);
}
elseif ($command == 'deposit2') //deposit operation
{
    BadIbank::ibankDeposit2 ($db, $langvars, $playerinfo);
}
elseif ($command == 'transfer') //main transfer menu
{
    BadIbank::ibankTransfer ($db, $langvars);
}
elseif ($command == 'transfer2') //specific transfer menu (ship or planet)
{
    BadIbank::ibankTransfer2 ($db);
}
elseif ($command == 'transfer3') //transfer operation
{
    BadIbank::ibankTransfer3 ($db);
}
elseif ($command == 'loans') //loans menu
{
    BadIbank::ibankLoans ($db, $langvars, $bntreg, $playerinfo);
}
elseif ($command == 'borrow') //borrow operation
{
    BadIbank::ibankBorrow ($db, $langvars, $playerinfo, $active_template);
}
elseif ($command == 'repay') //repay operation
{
    BadIbank::ibankRepay ($db, $langvars, $playerinfo);
}
elseif ($command == 'consolidate') //consolidate menu
{
    BadIbank::ibankConsolidate ($langvars);
}
elseif ($command == 'consolidate2') //consolidate compute
{
    BadIbank::ibankConsolidate2 ($db, $langvars, $playerinfo);
}
elseif ($command == 'consolidate3') //consolidate operation
{
    BadIbank::ibankConsolidate3 ($db, $langvars, $playerinfo);
}
else
{
  echo "
  <tr>
    <td width='25%' valign='bottom' align='left'><a href=\"main.php\">" . $langvars['l_ibank_quit'] . "</a></td>
    <td width='50%' style='text-align:left;'>
  <pre style='text-align:left;' class='term'>
  IIIIIIIIII          GGGGGGGGGGGGG    BBBBBBBBBBBBBBBBB
  I::::::::I       GGG::::::::::::G    B::::::::::::::::B
  I::::::::I     GG:::::::::::::::G    B::::::BBBBBB:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::B     B:::::B
    I::::I     G:::::G       GGGGGG      B::::B     B:::::B
    I::::I    G:::::G                    B::::B     B:::::B
    I::::I    G:::::G                    B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGGGGGGG      B:::::::::::::BB
    I::::I    G:::::G    G::::::::G      B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGG::::G      B::::B     B:::::B
    I::::I    G:::::G        G::::G      B::::B     B:::::B
    I::::I     G:::::G       G::::G      B::::B     B:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::BBBBBB::::::B
  I::::::::I     GG:::::::::::::::G    B:::::::::::::::::B
  I::::::::I       GGG::::::GGG:::G    B::::::::::::::::B
  IIIIIIIIII          GGGGGG   GGGG    BBBBBBBBBBBBBBBBB
  </pre>
  <center class='term'>
  ";
  echo $langvars['l_ibank_title'];
  echo "(tm)<br>";
  echo $langvars['l_ibank_humor'];
  echo "<br>&nbsp;
  </center>
  </td>
  <td width='25%' valign='bottom' align='right'><a href=\"igb.php?command=login\">" . $langvars['l_ibank_login'] . "</a></td>
  ";
}

?>

</table>
</td></tr>
</table>
</div>
<?php
echo '<img src="' . $template->GetVariables('template_dir') . '/images/div2.png" alt="" style="width: 600px; height:21px">';
echo '</center>';
include './footer.php';
?>
