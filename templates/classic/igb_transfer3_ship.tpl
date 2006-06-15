<form action="igb_transfer3.php" method="post">
  <table width="600" height="350" border="0">
    <tr>
      <td align="center" background="templates/{$templateset}/images/igbscreen.png">
<table width="520" height="300" border="0">
  <tr>
    <td colspan="2" align="center" valign="top">
      <font color="#00FF00">{$l_igb_transfersuccessful}<br>---------------------------------</font>
    </td>
  </tr>
  <tr valign="top">
    <td colspan="2" align="center">
      <font color="#00FF00">{$formatted_trans_to} {$l_igb_creditsto} {$target_character_name}</font>
    </td>
  </tr>
  <tr valign="top">
    <td>
      <font color="#00FF00">{$l_igb_transferamount} :</font>
    </td>
    <td align="right">
      <font color="#00FF00">{$formatted_trans_amt} C<br></font>
      <tr valign="top">
        <td>
          <font color="#00FF00">{$l_igb_transferfee} :</font>
        </td>
        <td align="right">
            <font color="#00FF00">{$formatted_trans_fee} C<br></font>
            <tr valign="top">
          <td><font color="#00FF00">{$l_igb_amounttransferred} :</td><td align="right">
<font color="#00FF00">{$formatted_amt_transd} C<br>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_igbaccount} :</td>
          <td align="right">
          <font color="#00FF00">{$formatted_acc_balance} C<br></font>
          <tr valign="bottom">
            <td>
              <font color="#00FF00"><a href="igb_login.php">{$l_igb_back}</a></font>
            </td>
            <td align="right">
              <font color="#00FF00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></font>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
