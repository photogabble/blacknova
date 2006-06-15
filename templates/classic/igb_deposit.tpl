{literal}
<style type="text/css">
    input.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}
    select.term {background-color: #000; color: #00FF00; font-size:1em; border-color:#00FF00;}
</style>
{/literal}

<div style="text-align:center;">
<img alt="" src="templates/{$templateset}/images/div1.png">

<table width="600" height="350" border="0">
  <tr>
    <td align="center" background="templates/{$templateset}/images/igbscreen.png">
      <table width="520" height="300" border="0">
        <tr><td colspan="2" align="center" valign="top"><font color="#00FF00">{$l_igb_depositfunds}<br>---------------------------------</td></tr>
        <tr valign="top">
          <td><font color="#00FF00">{$l_igb_fundsavailable} :</td>
          <td align="right"><font color="#00FF00">{$playerinfo_credits} C<br></td>
        </tr>
        <tr valign="top">
          <td><font color="#00FF00">{$l_igb_seldepositamount} :</td>
          <td align="right">
            <form action="igb_deposit2.php" method="post">
              <input class="term" type="text" size="15" maxlength="50" name="amount" value="0"><br><br>
              <input class="term" type="submit" value="{$l_igb_deposit}">
            </form>
          </td>
        </tr>
        <tr valign="bottom">
          <td><font color="#00FF00"><a href="igb_login.php">{$l_igb_back}</a></td>
          <td align="right"><font color="#00FF00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<img alt="" src="templates/{$templateset}/images/div2.png">
</div>

