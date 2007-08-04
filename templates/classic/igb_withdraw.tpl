<table width="600" height="350" border="0">
  <tr>
    <td align="center" background="templates/{$templateset}/images/igbscreen.png">
      <table width="520" height="300" border="0">
        <tr><td colspan="2" align="center" valign="top"><font color="#00FF00">{$l_igb_withdrawfunds}<br>---------------------------------</font></td></tr>
        <tr valign="top">
          <td><font color="#00FF00">{$l_igb_fundsavailable} :</font></td>
          <td align="right"><font color="#00FF00">{$account_balance} C<br></font></td>
        </tr>
        <tr valign="top">
          <td><font color="#00FF00">{$l_igb_selwithdrawamount} :</font></td>
          <td align="right">
            <form action="igb_withdraw2.php" method="post" accept-charset="utf-8">
              <input class="term" type="text" size="15" maxlength="50" name="amount" value="0"><br><br>
              <input class="term" type="submit" value="{$l_igb_withdraw}">
            </form>
          </td>
        </tr>
        <tr valign="bottom">
          <td><font color="#00FF00"><a href="igb_login.php">{$l_igb_back}</a></font></td>
          <td align="right"><font color="#00FF00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></font></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
