<table width="600" height="350" border="0">
  <tr>
    <td align="center" background="templates/{$templateset}/images/igbscreen.png">
      <table width="520" height="300" border="0">
        <tr><td colspan="2" align="center" valign="top"><font color="#00ff00">{$l_igb_welcometoigb}<br>---------------------------------</font></td></tr>
        <tr valign="top">
          <td><font color="#00ff00">{$l_igb_accountholder} :<br>{$l_igb_shipaccount} :<br>{$l_igb_igbaccount}&nbsp;&nbsp;:</font></td>
          <td align="right"><font color="#00ff00">{$character_name}<br>{$player_credits} {$l_igb_credit_symbol}<br> {$account_balance} {$l_igb_credit_symbol}<br></font></td>
        </tr>
        <tr>
          <td colspan="2" align="center"><font color="#00ff00">{$l_igb_operations}<br>---------------------------------<br><br>
            <a href="igb_withdraw.php">{$l_igb_withdraw}</a><br>
            <a href="igb_deposit.php">{$l_igb_deposit}</a><br>
            <a href="igb_transfer.php">{$l_igb_transfer}</a><br>
            <a href="igb_loans.php">{$l_igb_loans}</a><br>
{if $igb_consolidate_allowed}
            <a href="igb_consol.php">{$l_igb_consolidate}</a><br></font></td>
{else}
            <br></font></td>
{/if}
        </tr>
        <tr>
          <td colspan="2" align="center"><font color="#00ff00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
